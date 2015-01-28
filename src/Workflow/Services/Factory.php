<?php
namespace Lavender\Workflow\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Lavender\Workflow\Exceptions\StateException;

class Factory
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var string workflow name
     */
    protected $workflow;

    /**
     * @param Session $session
     * @param Config $config
     * @param Validator $validator
     * @param Renderer $renderer
     */
    public function __construct(Session $session, Config $config, Validator $validator, Renderer $renderer)
    {
        $this->session  = $session;

        $this->config  = $config;

        $this->validator  = $validator;

        $this->renderer  = $renderer;
    }

    /**
     * Get the evaluated view contents for the given workflow.
     *
     * @param  string $workflow
     * @return $this
     */
    public function make($workflow, $params = [])
    {
        $this->workflow = $workflow;

        $this->config->params($params);

        return $this;
    }

    public function post($state, $request, $errors = null)
    {
        $response = Redirect::back();

        try{

            $workflow = $this->workflow;

            if($state == $this->getState()){

                // load fields
                $fields = $this->config->get($workflow, $state, 'fields', []);

                // flash input into session
                $this->session->flash($fields);

                // validate request
                $this->validator->run($fields, $request);

                // fire callbacks
                Event::fire("workflow.{$workflow}.{$state}.after", [$request]);

                // all states
                $states = $this->config->get($workflow, null, 'states');

                // current state index
                $curr = array_search($state, $states);

                // if next index available, set new state
                if(isset($states[$curr + 1])){

                    $state = $states[$curr + 1];

                    $this->session->set($workflow, $state);

                } else{

                    // end of workflow, remain on last step

                }

                //todo return state response
                //$response = Redirect::back();

            }

        } catch(StateException $e){

            $errors = $e->getErrors()->messages();

        } catch(QueryException $e){

            //todo log exception
            $errors = ["Database error."];

        } catch(\Exception $e){

            //todo log exception
            $errors = [$e->getMessage()];

        }

        if($errors) $response->withErrors($errors, $workflow . '_' . $state);

        return $response;
    }

    protected function getState()
    {
        // all states
        $states = $this->config->get($this->workflow, null, 'states');

        // current state
        $state = $this->session->find($this->workflow, $states);

        // current state index
        $curr = array_search($state, $states);

        // unknown state
        if($curr === false) return false;

        return $state;
    }


    public function __toString()
    {
        try{

            if(isset($this->workflow) && $state = $this->getState()){

                $template = $this->config->get($this->workflow, $state, 'template');

                $options = $this->config->get($this->workflow, $state, 'options', []);

                $fields = $this->config->get($this->workflow, $state, 'fields', []);

                return $this->renderer->render($this->workflow, $state, $template, $options, $fields);
            }

        } catch(\Exception $e){

            // todo log exception
            return "Error rendering workflow: ".$e->getMessage()."<pre>".$e->getTraceAsString();

        }

        return '';
    }

}