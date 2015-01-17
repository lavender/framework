<?php
namespace Lavender\Workflow\Services;

class Session
{

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * Find the current state from session
     * @param $workflow
     * @param $states
     * @return mixed
     */
    public function find($workflow, $states)
    {
        if(!isset($this->resolved[$workflow])){

            $this->resolved[$workflow] = $this->findOrNew($workflow, $states);

        }

        return $this->resolved[$workflow];
    }

    /**
     * Find the next state and set it in session
     * @param $workflow
     * @param $state
     * @param $states
     * @return mixed
     */
    public function next($workflow, $state, $states)
    {
        $curr = array_search($state, $states);

        if(isset($states[$curr + 1])) $state = $states[$curr + 1];

        $this->set($workflow, $state);

        return $state;
    }

    /**
     * Find by session or create new
     * @return mixed
     */
    private function findOrNew($workflow, $states)
    {
        if($state = $this->get($workflow)){

            if(in_array($state, $states)) return $state;

        }

        $state = reset($states);

        $this->set($workflow, $state);

        return $state;
    }

    /**
     * @param $workflow
     * @return mixed
     */
    private function get($workflow)
    {
        return \Session::get("workflow.{$workflow}");
    }

    /**
     * @param $workflow
     * @param $state
     */
    private function set($workflow, $state)
    {
        \Session::put("workflow.{$workflow}", $state);
    }

}
