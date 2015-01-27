<?php
namespace Lavender\Workflow\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Lavender\Support\Contracts\WorkflowContract;
use Lavender\Support\Contracts\WorkflowInterface;

class Resolver
{
    /**
     * The resolved workflows.
     *
     * @var array
     */
    protected $resolved;

    /**
     * All workflow config
     * @var array
     */
    protected $config;

    protected $field_defaults = [
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

    public function resolve(WorkflowInterface $workflow)
    {
        if(!isset($this->resolved[$workflow->workflow])){

            // get array of all workflow classes
            $classes = $this->config[$workflow->workflow];

            // sort into ascending priority
            ksort($classes);

            $config = [];

            foreach($classes as $class){

                $model = new $class();

                if($model instanceof WorkflowContract){

                    $states = $model->states($workflow->workflow);

                    foreach($states as $state){

                        // set defaults
                        if(!isset($config[$state])){

                            $baseUrl = Config::get('store.workflow_base_url');

                            $config[$state] = [
                                'fields' => [],
                                'options' => [
                                    'method' => 'post',
                                    'url' => URL::to($baseUrl.'/'.$workflow->workflow.'/'.$state)

                                ],
                            ];

                        }

                        if(method_exists($model, $state)){

                            // collect fields
                            $fields = recursive_merge(
                                $config[$state]['fields'],
                                $model->$state($workflow)
                            );

                            // merge defaults
                            foreach($fields as $key => $data){

                                $fields[$key] = recursive_merge(
                                    $this->field_defaults,
                                    $data
                                );

                            }

                            // set fields for state
                            $config[$state]['fields'] = $fields;

                        }

                        if(method_exists($model, 'options')){

                            // collect the options; options are passed to Form:open()
                            $config[$state]['options'] = recursive_merge(
                                $config[$state]['options'],
                                $model->options($workflow->workflow, $state, $workflow)
                            );

                        }

                    }

                }

            }


            $this->resolved[$workflow->workflow] = $config;

        }
        return $this->resolved[$workflow->workflow];
    }

}
