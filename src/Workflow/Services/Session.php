<?php
namespace Lavender\Workflow\Services;

use Lavender\Support\Contracts\WorkflowInterface;

class Session
{

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * Find the current state from session
     * @param WorkflowInterface $workflow
     * @return mixed
     * @internal param $states
     */
    public function find(WorkflowInterface $workflow)
    {
        if(!isset($this->resolved[$workflow->workflow])){

            $this->resolved[$workflow->workflow] = $this->findOrNew($workflow);

        }

        return $this->resolved[$workflow->workflow];
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
     * @param WorkflowInterface $workflow
     * @return mixed
     */
    private function findOrNew(WorkflowInterface $workflow)
    {
        if($state = $this->get($workflow->workflow)){

            if(in_array($state, $workflow->states)) return $state;

        }

        $state = reset($workflow->workflow);

        $this->set($workflow->workflow, $state);

        return $state;
    }

    /**
     * @param $workflow
     * @return mixed
     */
    private function get($name)
    {
        return \Session::get("workflow.{$name}");
    }

    /**
     * @param $workflow
     * @param $state
     */
    private function set($name, $state)
    {
        \Session::put("workflow.{$name}", $state);
    }

}
