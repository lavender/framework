<?php
namespace Lavender\Workflow\Services;

use Illuminate\Support\Facades\Input;

class Session
{
    protected $resolved = [];

    /**
     * Find the current state from session
     * @param string $workflow
     * @param array $states
     * @return array
     */
    public function find($workflow, array $states)
    {
        if(!isset($this->resolved[$workflow])){

            if(!$state = $this->get($workflow)){

                $state = reset($states);

                $this->set($workflow, $state);

            }

            $this->resolved[$workflow] = $state;

        }

        return $this->resolved[$workflow];
    }

    /**
     * Flash only fields where flash = true
     *
     * @param array $fields
     */
    public function flash(array $fields)
    {
        $flash = array_where($fields, function($key, $config){

            return $config['flash'];

        });

        Input::flashOnly(array_keys($flash));
    }

    /**
     * @param string $workflow
     * @return mixed
     */
    protected function get($workflow)
    {
        return \Session::get("workflow.{$workflow}");
    }

    /**
     * @param string $workflow
     * @param string $state
     */
    public function set($workflow, $state)
    {
        \Session::put("workflow.{$workflow}", $state);
    }

}
