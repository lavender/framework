<?php
namespace Lavender\Workflow\Services;

use Lavender\Support\Contracts\WorkflowContract;

class Config
{
    /**
     * The resolved workflows.
     *
     * @var array
     */
    protected $resolved;

    /**
     * Params passed to workflow when created
     * @var array
     */
    protected $params;

    /**
     * All workflow config
     * @var array
     */
    protected $config;

    protected $defaults = [
        // field label (optional)
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

        //applies to select fields
        'values' => [],

        //applies to checkbox & radio fields
        'checked' => [],
    ];


    public function __construct($config)
    {
        $this->config = $config;
    }

    public function params($params)
    {
        $this->params = $params;
    }

    public function get($workflow, $state, $param, $default = null)
    {
        $resolved = $this->resolve($workflow);

        if($param == 'states') return array_keys($resolved);

        return isset($resolved[$state][$param]) ? $resolved[$state][$param] : $default;
    }

    protected function resolve($workflow)
    {
        if(!isset($this->resolved[$workflow])){

            // get array of all workflow classes
            $classes = $this->config[$workflow];

            // sort into ascending priority
            ksort($classes);

            $config = [];

            foreach($classes as $class){

                $model = new $class();

                if($model instanceof WorkflowContract){

                    $states = $model->states();

                    foreach($states as $state){

                        // set defaults
                        if(!isset($config[$state])){

                            $config[$state] = [
                                'fields' => [],
                                'options' => ['method' => 'post'],
                                'template' => null,
                            ];

                        }

                        if(method_exists($model, $state)){

                            // collect fields
                            $fields = recursive_merge(
                                $config[$state]['fields'],
                                $model->$state($this->params)
                            );

                            // merge defaults
                            foreach($fields as $key => $data){

                                $fields[$key] = recursive_merge(
                                    $this->defaults,
                                    $data
                                );

                            }

                            // set fields for state
                            $config[$state]['fields'] = $fields;

                        }

                        if($template = $model->template($state)){

                            // template used to render the form
                            $config[$state]['template'] = $template;

                        }

                        if($options = $model->options($state)){

                            // collect the options; options are passed to Form:open()
                            $config[$state]['options'] = recursive_merge(
                                $config[$state]['options'],
                                $options
                            );

                        }

                    }

                }

            }


            $this->resolved[$workflow] = $config;

        }
        return $this->resolved[$workflow];
    }

}
