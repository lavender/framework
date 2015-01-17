<?php
namespace Lavender\Workflow\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Lavender\Support\Contracts\WorkflowContract;

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

    public function resolve($workflow)
    {
        if(!isset($this->resolved[$workflow])){

            // get array of all workflow classes
            $classes = $this->config[$workflow];

            // sort into ascending priority
            ksort($classes);

            $config = [];

            foreach($classes as $class){

                $model = new $class;

                if($model instanceof WorkflowContract){

                    $states = $model->states($workflow);

                    foreach($states as $state){

                        // set defaults
                        if(!isset($config[$state])){

                            $baseUrl = Config::get('store.workflow_base_url');

                            $config[$state] = [
                                'fields' => [],
                                'options' => [
                                    'method' => 'post',
                                    'url' => URL::to($baseUrl.'/'.$workflow.'/'.$state)

                                ],
                            ];

                        }

                        // collect fields passed to Renderer
                        if(method_exists($model, $state)){

                            $fields = recursive_merge(
                                $config[$state]['fields'],
                                $model->$state()
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
                                $model->options($workflow, $state)
                            );

                        }

                    }

                }

            }


            $this->resolved[$workflow] = $config;

        }
        return $this->resolved[$workflow];
    }

//
//    public function redirect($workflow, $state)
//    {
//        $redirect = $this->config[$workflow][$state]['redirect'];
//
//        return $redirect ? Redirect::to($redirect) : Redirect::back();
//    }
//
//    public function hasState($workflow, $state)
//    {
//        return isset($this->config[$workflow][$state]);
//    }
//
//    public function defaultState($workflow)
//    {
//        return array_keys($this->states($workflow))[0];
//    }
//
//    public function nextState($workflow, $state)
//    {
//        $states = array_keys($this->states($workflow));
//
//        $curr = array_search($state, $states);
//
//        if(isset($states[$curr + 1])) return $states[$curr + 1];
//
//        return $state;
//    }

}
