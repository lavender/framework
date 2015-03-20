<?php
namespace Lavender\Support;

use Illuminate\Queue\SerializesModels;
use Lavender\Contracts\Workflow as WorkflowContract;

abstract class Workflow implements WorkflowContract
{

    use SerializesModels;

    public $template = '';

    public $fields = [];

    public $options = ['method' => 'post'];


    /**
     * Create a new workflow instance.
     */
    abstract public function __construct($params);


    /**
     * Add a field to the workflow
     *
     * @param $field
     * @param array $data
     */
    public function addField($field, array $data)
    {
        $this->fields[$field] = $this->prepareField($data);
    }


    /**
     * Get array of fields.
     *
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->fields);
    }


    /**
     * Get data from an existing field.
     *
     * @param string $field
     * @param string $key
     * @return mixed
     */
    public function getFieldData($field, $key)
    {
        return $this->fields[$field][$key];
    }


    /**
     * Add data to existing field.
     *
     * @param string $field
     * @param string $key
     * @param mixed $value
     */
    public function setFieldData($field, $key, $value)
    {
        $this->fields[$field][$key] = $value;
    }


    /**
     * Merge field defaults
     *
     * @param array $data
     * @return array
     */
    protected function prepareField(array $data)
    {
        return array_merge([

            /** Rendering config */
            'default' => null, // todo test null vs empty
            'position' => 0,

            /** Handler config */
            'flash' => true, // which fields to flash into session
            'validate' => [], // field validation rules

            /** HTML config */
            'label' => null, // html label
            'label_options' => [], // label options
            'comment' => null, // string comment

            /** Field config */
            'type' => 'text', // type alias
            'name' => null, // field name
            'value' => null, // field value
            'options' => ['id' => null], // field options
            'resource' => null,
            'use_default' => false,// bool|string, adds checkbox to disable field

        ], $data);
    }


}
