<?php
namespace Lavender\Support\Traits;

use Lavender\Contracts\Entity;

trait EntityShorthandTrait
{


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
     * Used to translate shorthand
     * @param $original
     * @param $callback
     * @param array $userdata
     */
    private function array_walk(&$original, $callback, $userdata = [])
    {
        $resolved = (array)$original;

        array_walk($resolved, $callback, $userdata);

        if(count((array)$original) == 1) $resolved = reset($resolved);

        $original = $resolved;
    }

    /**
     * Translates $value into Entity
     * Expected syntax: ['entity' => ['attribute' => 'value']] or ['entity' => id] or ['entity' => [id, id, id]]
     *
     * @param array $value
     * @param string $entity
     */
    private function _translate(&$value, $entity)
    {
        $attribute = 'id';

        if(is_array($value) && !is_numeric(key($value))){

            $attribute = key($value);

        }

        $this->array_walk($value, [$this, '_make'], [$entity, $attribute]);
    }

    /**
     * Converts current $value to Entity
     * @param $value
     * @param $index (not used)
     * @param $userdata
     */
    private function _make(&$value, $index, $userdata)
    {
        list($entity, $attribute) = $userdata;

        var_dump($userdata, $value);

        $value = entity($entity)->findByAttribute($attribute, $value);
    }
}