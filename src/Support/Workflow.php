<?php
namespace Lavender\Support;

use Illuminate\Queue\SerializesModels;
use Lavender\Contracts\Workflow as WorkflowContract;

abstract class Workflow implements WorkflowContract
{

    use SerializesModels;

    public $fields = [];

    public $template = 'workflow.form';

    public $options = [];


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
        $this->fields[$field] = $this->mergeDefaults($data);
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
    protected function mergeDefaults(array $data)
    {
        return array_merge([
            // field label
            'label' => null,
            'label_options' => [],

            // applies to all fields
            'type' => 'text',
            'position' => 0,
            'name' => null,
            'value' => null,
            'options' => ['id' => null],
            'validate' => [],
            'comment' => null,
            'flash' => true,

            //applies to select fields and tables
            'values' => [],

            //applies to tables
            'headers' => [],

            //applies to checkbox & radio fields
            'checked' => [],
        ], $data);
    }


    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        if($field = isset($args[0]) ? $args[0] : false){

            $key = snake_case(substr($method, 3));

            switch(substr($method, 0, 3)){

                case 'get' :

                    return $this->getFieldData($field, $key);

                case 'set' :

                    $value = isset($args[1]) ? $args[1] : null;

                    $this->setFieldData($field, $key, $value);

                    return true;

            }

        }

        throw new \Exception("Undefined method {$method}.");
    }


}
