<?php
namespace Lavender\Database;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Lavender\Support\Traits\AttributeTrait;
use Lavender\Support\Traits\EntityShorthandTrait;
use Lavender\Support\Traits\RelationshipTrait;
use Lavender\Contracts\Entity as EntityContract;

abstract class Entity extends Eloquent implements EntityContract
{
    use AttributeTrait, RelationshipTrait, EntityShorthandTrait;

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
     * Get the model's config name
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entity;
    }

    /**
     * Get the model's attributes
     *
     * @return string
     */
    public function getAttributeConfig()
    {
        return $this->config['attributes'];
    }

    /**
     * Get the model's relationships
     *
     * @return string
     */
    public function getRelationshipConfig()
    {
        return $this->config['relationships'];
    }

    /**
     * Get the model's scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->config['scope'];
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

            if($relationship = $this->resolveRelationship($key)){

                $attribute = $relationship->getResults();

                $this->relations[$key] = $relationship;
            }
        }

        return $attribute;
    }

    public function fill(array $attributes, $prepare = true)
    {
        // Make sure the entity configuration is loaded
        // before we attempt to fill the model.
        $this->prepareConfig();

        // This let's us use our relationship aliases to
        // set relationships as they are being filled.
        if($prepare) $this->prepareAttributes($attributes);

        return parent::fill($attributes);
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
     * @param $key
     * @return bool
     * @throws \Exception
     */
    protected function resolveAttribute($key)
    {
        if(isset($this->config['attributes'][$key])){

            $handler = $this->config['attributes'][$key]['handler'];

            return new $handler($this, $key);

        } elseif($this->isNativeAttribute($key)){

            return new Attribute($this, $key);

        }

        return false;
    }

    /**
     * Determine if key is a native attribute
     * @param $key
     * @return bool
     */
    protected function isNativeAttribute($key)
    {
        if($this->timestamps && in_array($key, ['updated_at', 'created_at'])){

            return true;

        } elseif($key == $this->primaryKey){

            return true;

        }

        return false;
    }

    /**
     * @param $key
     * @return BelongsTo|BelongsToMany|HasMany|HasOne|null
     * @throws \Exception
     */
    protected function resolveRelationship($key)
    {
        $attribute = null;

        if(isset($this->config['relationships'][$key])){

            $relationship = $this->config['relationships'][$key];

            $model = entity($relationship['entity']);

            $onEntity = snake_case($relationship['entity']);

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
     * @return QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $this->prepareConfig();

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
     * Reload the configuration and set fillable attributes.
     */
    protected function prepareConfig()
    {
        $config = config("entity", []);

        $entity = $this->entity;

        if(!isset($config[$entity])){

            throw new \Exception("No configuration found for entity {$entity}.");

        }

        // assign the given config
        $this->config = $config[$entity] + [
            'attributes'    => [],
            'relationships' => [],
            'scope'         => Scope::DISABLED,
        ];

        // merge attribute defaults
        foreach($this->config['attributes'] as $attr => $values){

            $this->config['attributes'][$attr] = $this->applyAttributeDefaults($values);

        }

        // merge relationship defaults
        foreach($this->config['relationships'] as $rel => $values){

            $this->config['relationships'][$rel] = $this->applyRelationshipDefaults($values);
        }

        $this->fillable(array_keys($this->config['attributes']));
    }

    /**
     * Save various model relationships; used when creating a new entity
     *
     * @param array $attributes
     * @throws \Exception Unknown relationship type
     */
    protected function prepareAttributes(&$attributes)
    {
        foreach($attributes as $key => $value){

            if(isset($this->config['attributes'][$key])){

                $attributes[$key] = $this->$key()->before_save($value);

            } elseif(isset($this->config['relationships'][$key])){

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
     * Handle dynamic method calls into the method.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if($attribute = $this->resolveAttribute($method)){
            return $attribute;
        }
        if($relationship = $this->resolveRelationship($method)){
            return $relationship;
        }

        return parent::__call($method, $parameters);
    }
}