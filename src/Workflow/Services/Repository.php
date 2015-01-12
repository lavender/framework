<?php
namespace Lavender\Workflow\Services;


class Repository
{

    protected $resolver;

    /**
     * @var array
     */
    protected $instances;


    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function config($workflow)
    {
        return $this->resolver->states($workflow);
    }

    /**
     * Find workflow or create new
     * @return mixed
     */
    public function find($workflow)
    {
        if(!isset($this->instances[$workflow])){

            $this->instances[$workflow] = $this->findOrNew($workflow);

        }

        return $this->instances[$workflow];
    }

    /**
     * Find by session
     * @return mixed
     */
    private function findOrNew($workflow)
    {
        if($found = $this->get($workflow)){

            if($this->resolver->hasState($workflow, $found->state)) return $found;

        }

        return $this->first($workflow);
    }

    /**
     * Set default state
     */
    private function first($workflow)
    {
        $first = $this->resolver->defaultState($workflow);

        return $this->setState($workflow, $first);
    }

    /**
     * Set next state
     * @param $workflow
     * @param $state
     * @return mixed
     */
    public function next($workflow, $state)
    {
        $next = $this->resolver->nextState($workflow, $state);

        return $this->setState($workflow, $next);
    }

    public function redirect($workflow, $state)
    {
        return $this->resolver->redirect($workflow, $state);
    }

    /**
     * @param $state
     * @return mixed
     */
    private function setState($workflow, $state)
    {
        $values = ['state' => $state];

        $this->put($workflow, (object)$values);

        return $this->get($workflow);
    }

    private function get($workflow)
    {
        return \Session::get("workflow_{$workflow}");
    }


    private function put($workflow, $data)
    {
        \Session::put("workflow_{$workflow}", $data);
    }

}