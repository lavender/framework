<?php
namespace Lavender\Contracts;

interface Form
{

    /**
     * Add a field to the form.
     *
     * @param $field
     * @param array $data
     */
    public function addField($field, array $data);


    /**
     * Get array of fields.
     *
     * @return array
     */
    public function getFields();


    /**
     * Get data from an existing field.
     *
     * @param string $field
     * @param string $key
     * @return mixed
     */
    public function getFieldData($field, $key);


    /**
     * Add data to existing field.
     *
     * @param string $field
     * @param string $key
     * @param mixed $value
     */
    public function setFieldData($field, $key, $value);

}