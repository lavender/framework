<?php
namespace Lavender\Database;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Lavender\Support\Traits\AttributeTrait;
use Lavender\Support\Traits\RelationshipTrait;
use Lavender\Support\Contracts\EntityInterface;
use Lavender\Support\Facades\Relationship;
use Lavender\Support\Facades\Scope;

abstract class Entity extends Eloquent implements EntityInterface
{

    use AttributeTrait, RelationshipTrait;
    /**
     * Unique model name
     *
     * @var string $entity
     */
    protected $entity;

    /**
     * Entity configuration
     *
     * @var array $config
     */
    private $config;

    /**
     * Reload the configuration and set fillable attributes.
     * todo move defaults to where they apply
     */
    public function reload()
    {
        $config = config("entity", []);

        if(!isset($config[$this->entity])) throw new \Exception("No configuration found for entity {$this->entity}.");

        // merge config defaults
        $this->config = array_merge([
                'attributes'    => [],
                'relationships' => [],
                'scope'         => Scope::IS_GLOBAL,
            ],
            $config[$this->entity]
        );

        // merge attribute defaults
        foreach($this->config['attributes'] as $attr => $values){

            $this->config['attributes'][$attr] = $this->applyAttributeDefaults($values);

        }

        // merge relationship defaults
        foreach($this->config['relationships'] as $rel => $values){

            $this->config['relationships'][$rel] = $this->applyRelationshipDefaults($values);
        }

        $this->fillable = array_keys($this->config['attributes']);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function backendLabel($key)
    {
        return $this->frontendLabel($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function frontendLabel($key)
    {
        if(isset($this->config['attributes'][$key])){

            return $this->config['attributes'][$key]['label'] ? : $key;

        }

        return $key;
    }

    /**
     * Render an attribute by key
     * @param $key
     * @return mixed
     */
    public function backendValue($key)
    {
        return $this->decorateValue($key, 'backend');
    }

    /**
     * Render an attribute by key
     * @param $key
     * @return mixed
     */
    public function frontendValue($key)
    {
        return $this->decorateValue($key, 'frontend');
    }

    /**
     * Load the attributes renderer if available
     * else use default renderer (returns raw value)
     * todo entity attributes as objects
     * @param $key
     * @param $type
     * @return mixed
     */
    protected function decorateValue($key, $type)
    {
        $renderer = $type . '.renderer';

        if(isset($this->config['attributes'][$key][$renderer])){

            if($renderable = $this->config['attributes'][$key][$renderer]){

                $renderable = new $renderable;

            } else {

                $renderable = app('attribute.renderer');

            }

            return $renderable->render($this, $key);
        }

        return $this->$key;
    }

    /**
     * @return array
     */
    public function backendTable()
    {
        return ['id' => null] + array_where($this->getConfig('attributes'), function($key, $value){

            return $value['backend.table'] !== null;

        });
    }


    /**
     * Load the first entity by it's attribute
     *
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public static function findByAttribute($attribute, $value, $columns = ['*'])
    {
        $model = new static;

        return $model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string $key
     * @throws \Exception
     * @return mixed
     */
    public function getAttribute($key)
    {
        // If the attribute is not already available to the
        // model, let's look for any configured relationships
        // that match the $key and return a Collection|static
        if(!$attribute = parent::getAttribute($key)){

            if($relationship = $this->getRelationship($key)){

                $attribute = $relationship->getResults();

                $this->relations[$key] = $relationship;
            }
        }

        return $attribute;
    }

    /**
     * @param $key
     * @throws \Exception
     */
    protected function getAttributeHandler($key)
    {
        if(isset($this->config['attributes'][$key])){

            $handler = $this->config['attributes'][$key]['handler'];

            return new $handler($this, $key);
        }

        return false;
    }

    /**
     * @param $key
     * @return BelongsTo|BelongsToMany|HasMany|HasOne|null
     * @throws \Exception
     */
    protected function getRelationship($key)
    {
        $attribute = null;

        if(isset($this->config['relationships'][$key])){

            $relationship = $this->config['relationships'][$key];

            $model = entity($relationship['entity']);

            $onEntity = snake_case($model->getEntity());

            $thisEntity = snake_case($this->entity);

            $localKey = "{$thisEntity}_id";

            $foreignKey = "{$onEntity}_id";

            switch($relationship['type']){
                case Relationship::HAS_PIVOT:

                    $attribute = $this->belongsToMany(
                        $model,
                        $relationship['table'],
                        $localKey,
                        $foreignKey,
                        "{$localKey}_{$foreignKey}"
                    );

                    break;

                case Relationship::HAS_MANY:

                    $attribute = $this->hasMany($model, $localKey);

                    break;

                case Relationship::HAS_ONE:

                    $attribute = $this->hasOne($model, $model->getKeyName());

                    break;

                case Relationship::BELONGS_TO:

                    $attribute = $this->belongsTo($model, $foreignKey, $model->getKeyName(), $key);

                    break;

                default:
                    throw new \Exception(sprintf(
                        "Unknown relationship type \"%s\" on entity \"%s\"",
                        $relationship['type'],
                        $relationship['entity']
                    ));
                    break;
            }
        }

        return $attribute;
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Lavender\Entity\Database\QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $this->reload();

        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        return new QueryBuilder(
            $conn,
            $grammar,
            $conn->getPostProcessor(),
            $this->config
        );
    }

    /**
     * Get the model's relationships
     *
     * @return string
     */
    public function getRelationships()
    {
        return $this->config['relationships'];
    }

    /**
     * Get the model's config name
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the model's config
     *
     * @return string
     */
    public function getConfig($node = null)
    {
        if($node) return $this->config[$node];

        return $this->config;
    }

    public function fill(array $attributes, $prepare = true)
    {
        // Make sure the entity configuration is loaded
        // before we attempt to fill the model.
        $this->reload();

        // This let's us use our relationship aliases to
        // set relationships as they are being filled.
        if($prepare) $this->prepareAttributes($attributes);

        return parent::fill($attributes);
    }

    /**
     * Save various model relationships; used when creating a new entity
     *
     * @param array $attributes
     * @throws \Exception Unknown relationship type
     */
    protected function prepareAttributes(&$attributes)
    {
        $relationships = array_keys($this->config['relationships']);

        foreach($this->config['attributes'] as $key => $config){

            if(isset($config['before_save']) && $config['before_save']){

                $before_save = new $config['before_save'];

                $value = isset($attributes[$key]) ? $attributes[$key] : null;

                $before_save->before_save($value);

                $attributes[$key] = $value;
            }
        }

        foreach($attributes as $key => $value){

            if(in_array($key, $relationships)){

                unset($attributes[$key]);

                $relationship = $this->config['relationships'][$key];

                $this->translateShorthand($value);

                switch($relationship['type']){

                    case Relationship::HAS_PIVOT:

                        if($value instanceof Collection) $value = $value->all();

                        $this->$key()->saveMany($value);

                        break;

                    case Relationship::HAS_MANY:

                        $this->$key()->saveMany((array)$value);

                        break;

                    case Relationship::BELONGS_TO:

                        $this->$key()->associate($value);

                        break;

                    case Relationship::HAS_ONE:

                        $this->$key()->save($value);

                        break;

                    default:

                        throw new \Exception(sprintf(
                            "Unknown relationship type \"%s\" on entity \"%s\"",
                            $relationship['type'],
                            $relationship['entity']
                        ));

                        break;
                }
            }
        }
    }

    /**
     * Translate shorthand:
     * We may want to pass shorthand representations of models
     * for importing, seeding, mass actions, etc..
     *
     * @param $value
     */
    protected function translateShorthand(&$value)
    {
        // $value is an array but not an array of Entity(s)
        if(is_array($value) && !current($value) instanceof Entity){

            $this->array_walk($value, [$this, '_translate']);
        }
    }

    /**
     * Translates $value into Entity
     * Expected syntax: ['entity' => ['attribute' => 'value']] or ['entity' => id]
     *
     * @param array $value
     * @param string $entity
     */
    private function _translate(&$value, $entity)
    {
        $has_key = is_array($value) && !is_numeric(key($value));

        $this->array_walk($value, [$this, '_make'], [$entity, $has_key ? key($value) : 'id']);
    }

    /**
     * Converts current $value to Entity
     * @param $value
     * @param $index
     * @param $userdata
     */
    private function _make(&$value, $index, $userdata)
    {
        list($entity, $attribute) = $userdata;

        $value = entity($entity)->findByAttribute($attribute, $value);
    }

    /**
     * @param $original
     * @param $callback
     * @param array $userdata
     */
    protected function array_walk(&$original, $callback, $userdata = [])
    {
        $resolved = (array)$original;

        array_walk($resolved, $callback, $userdata);

        if(count((array)$original) == 1) $resolved = reset($resolved);

        $original = $resolved;
    }

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if($attribute = $this->getAttributeHandler($method)){
            return $attribute;
        }
        if($relationship = $this->getRelationship($method)){
            return $relationship;
        }

        return parent::__call($method, $parameters);
    }
}