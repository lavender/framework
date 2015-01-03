<?php namespace Lavender\Workflow\Services;

use Lavender\Workflow\Interfaces\WorkflowInterface;
use Lavender\Workflow\Interfaces\ResolverInterface;

class Resolver implements ResolverInterface
{

    /**
     * The array of workflow resolvers.
     *
     * @var array
     */
    protected $resolvers = array();

    /**
     * The resolved workflow instances.
     *
     * @var array
     */
    protected $resolved = array();


    /**
     * Register a new workflow resolver.
     *
     * @param  string $workflow
     * @param  WorkflowInterface $model
     * @return void
     */
    public function register($workflow, WorkflowInterface $model)
    {
        $this->resolvers[$workflow] = $model;
    }


    /**
     * Resolve a workflow instance by name.
     *
     * @param  string $workflow
     * @return ModelInterface
     * @throws \InvalidArgumentException
     */
    public function resolve($workflow)
    {
        if(isset($this->resolved[$workflow])){
            return $this->resolved[$workflow];
        }

        if(isset($this->resolvers[$workflow])){
            return $this->resolved[$workflow] = $this->resolvers[$workflow];
        }

        throw new \InvalidArgumentException("Workflow $workflow not found.");
    }
}
