<?php
namespace Lavender\Workflow\Services;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\App;

class Factory
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * Instantiate a new workflow object
     *
     * @param Repository $repository
     * @param Dispatcher $events
     */
    public function __construct(Repository $repository, Dispatcher $events)
    {
        $this->repository = $repository;

        $this->events  = $events;
    }


    /**
     * Get the evaluated view contents for the given workflow.
     *
     * @param  string $workflow
     * @return ViewModel
     */
    public function make($workflow)
    {
        $found = $this->repository->find($workflow);

        $config = $this->repository->config($workflow);

        $view = App::make('workflow.view', [$workflow, $found->state, $config]);

        $this->events->fire("workflow.{$workflow}.{$found->state}", [$view]);

        return $view;
    }


    /**
     * Get the red view contents for the given workflow.
     *
     * @param  string $workflow
     * @return ViewModel
     */
    public function next($workflow, $state)
    {
        $found = $this->repository->next($workflow, $state);

        return $this->repository->redirect($workflow, $found->state);
    }

}