<?php
namespace Lavender\Workflow\Services;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;

class Factory
{
    /**
     * @var array
     */
    protected $repositories = [];

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var Redirect
     */
    protected $redirect;

    /**
     * @param Session $session
     * @param Resolver $resolver
     * @param Dispatcher $events
     */
    public function __construct(Session $session, Resolver $resolver, Validator $validator, Dispatcher $events)
    {
        $this->session  = $session;

        $this->resolver  = $resolver;

        $this->validator  = $validator;

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
        $config = $this->resolver->resolve($workflow);

        $state = $this->session->find($workflow, array_keys($config));

        $view = App::make('workflow.view')
            ->with('fields', $config[$state]['fields'])
            ->with('options', $config[$state]['options'])
            ->with('workflow', $workflow)
            ->with('state', $state);

        $this->events->fire("workflow.{$workflow}.{$state}.before", [$view]);

        return $view;
    }

    /**
     * Get the red view contents for the given workflow.
     *
     * @param  string $workflow
     * @param string $state
     * @param array $request
     * @return ViewModel
     */
    public function next($workflow, $state, $request, $response)
    {
        $this->redirect = $response;

        // get workflow config
        $config = $this->resolver->resolve($workflow);

        // find current state in session
        $current_state = $this->session->find($workflow, array_keys($config));

        if($current_state == $state){

            // validate request
            $this->validator->run($config[$state]['fields'], $request);

            // fire callbacks
            $this->events->fire("workflow.{$workflow}.{$state}.after", [$request]);

            // set next state
            $this->session->next($workflow, $state, array_keys($config));

        }

        return $this->redirect;
    }

    public function redirect($redirect)
    {
        if(is_string($redirect)) $redirect = Redirect::to($redirect);

        $this->redirect = $redirect;
    }
}