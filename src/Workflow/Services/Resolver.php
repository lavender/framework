<?php
namespace Lavender\Workflow\Services;


use Illuminate\Support\Facades\Redirect;

class Resolver
{

    /**
     * The sorted workflow states.
     *
     * @var array
     */
    protected $sorted;

    /**
     * All workflow config
     * @var array
     */
    protected $config;


    public function __construct($config)
    {
        $this->config = $config;
    }

    public function states($workflow)
    {
        if(!isset($this->sorted[$workflow])){

            $states = $this->config[$workflow];

            sort_children($states);

            $this->sorted[$workflow] = $states;

        }
        return $this->sorted[$workflow];
    }

    public function redirect($workflow, $state)
    {
        $redirect = $this->config[$workflow][$state]['redirect'];

        return $redirect ? Redirect::to($redirect) : Redirect::back();
    }

    public function hasState($workflow, $state)
    {
        return isset($this->config[$workflow][$state]);
    }

    public function defaultState($workflow)
    {
        return array_keys($this->states($workflow))[0];
    }

    public function nextState($workflow, $state)
    {
        $states = array_keys($this->states($workflow));

        $curr = array_search($state, $states);

        if(isset($states[$curr + 1])) return $states[$curr + 1];

        return $state;
    }

}
