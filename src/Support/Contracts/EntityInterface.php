<?php
namespace Lavender\Support\Contracts;

interface EntityInterface
{
    /**
     * Reload the configuration and set fillable attributes.
     */
    public function reload();

    /**
     * Render an attribute by key
     * @param $key
     * @return mixed
     */
    public function backendValue($key);

    /**
     * Render an attribute by key
     * @param $key
     * @return mixed
     */
    public function frontendValue($key);

    /**
     * Load the first entity by it's attribute
     *
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public static function findByAttribute($attribute, $value, $columns = ['*']);

    /**
     * Get the model's config name
     *
     * @return string
     */
    public function getEntity();

    /**
     * Get the model's config
     *
     * @return string
     */
    public function getConfig($node = null);
}