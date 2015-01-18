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

                        // collect fields passed to Renderer
                        if(method_exists($model, $state)){

                            $fields = recursive_merge(
                                $config[$state]['fields'],
                                $model->$state($workflow)
                            );


                            array_walk_depth($fields, 1, function(&$data){

                                merge_defaults($data, 'workflow');

                            });

                            $config[$state]['fields'] = $fields;

                        }

                        // collect the options passed to Form:open()
                        if(method_exists($model, 'options')){

                            $config[$state]['options'] = recursive_merge(
                                $config[$state]['options'],
                                $model->options($workflow->workflow, $state)
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
