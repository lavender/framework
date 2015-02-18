<?php
namespace Lavender\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Lavender\Exceptions\WorkflowException;
use Lavender\Services\Workflow\Renderer;
use Lavender\Services\Workflow\Session;
use Lavender\Services\Workflow\Validator;

class WorkflowFactory
{

    protected $workflow;

    protected $state;

    protected $config;

    protected $params;

    protected $session;

    protected $renderer;

    protected $validator;

    public function __construct(Session $session, Renderer $renderer, Validator $validator)
    {
        $this->session = $session;

        $this->renderer = $renderer;

        $this->validator = $validator;
    }

    public function getInstance()
    {
        return $this;
    }

    public function exists($workflow)
    {
        return isset(config('workflow')[$workflow]);
    }

    /**
     * Get the evaluated view contents for the given workflow.
     *
     * @param  string $workflow
     * @param array $params
     * @return $this
     */
    public function make($workflow, $params = [])
    {
        $this->workflow = $workflow;

        $this->params = $params;

        $this->config = config('workflow')[$workflow];

        ksort($this->config);

        $this->loadState();

        return $this;
    }

    /**
     * Handle workflow form submission
     *
     * @param array $request
     * @return mixed
     * @throws \Exception
     */
    public function post(array $request)
    {
        try{

            $workflow = $this->resolve();

            // flash input into session
            $this->session->flashInput($workflow->fields);

            // validate request
            $this->validator->run($workflow->fields, $request);

            // fire callbacks
            $workflow->request = $request;

            event($workflow);

            // go to next state
            $this->nextState();

        } catch(WorkflowException $e){

            // workflow validation errors
            $this->session->setErrors($this->workflow, $e->getErrors()->messages());

        } catch(QueryException $e){

            // database errors
            //todo log "Database error.";

        } catch(\Exception $e){

            // general exceptions
            //todo log $e->getMessage();

        }
    }

    /**
     * Render the current workflow
     * @param string $output
     * @return string
     */
    public function render()
    {
        $output = '';

        try{

            $workflow = $this->resolve();

            $errors = $this->session->getErrors($this->workflow);

            $output = $this->renderer->render($workflow, $errors);

        } catch(\Exception $e){

            // todo log exception
            $output = $e->getMessage();

        }

        return $output;
    }

    protected function updateSession()
    {
        $this->session->setState($this->workflow, $this->state);
    }


    protected function nextState()
    {
        // all states
        $states = $this->states();

        $curr = array_search($this->state, $states);

        if($state = isset($states[$curr + 1]) ? $states[$curr + 1] : false){

            $this->state = $state;

            $this->updateSession();

        }
    }

    protected function loadState()
    {
        $this->state = $this->session->getState($this->workflow);

        if($this->state === false){

            $states = $this->states();

            $this->state = reset($states);

            $this->updateSession();
        }
    }

    protected function states()
    {
        return array_keys($this->config);
    }

    protected function resolve()
    {
        return new $this->config[$this->state]($this->params);
    }

    public function __toString()
    {
        return $this->render();
    }
}