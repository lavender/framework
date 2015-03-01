<?php
namespace Lavender\Workflow;

use Illuminate\Contracts\Events\Dispatcher;
use Lavender\Contracts\Workflow;
use Lavender\Contracts\Workflow\Kernel as WorkflowKernel;

class Kernel implements WorkflowKernel
{

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var array
     */
    protected $workflowForms;

    /**
     * @var array
     */
    protected $workflowFields;

    /**
     * @var array
     */
    protected $workflowHandlers;

    /**
     * @var string
     */
    protected $workflowTemplate;

    /**
     * Initialize the workflow kernel.
     *
     * @param Dispatcher $events
     * @param Session $session
     * @param Renderer $renderer
     * @param Validator $validator
     * @throws \Exception
     */
    public function __construct(Dispatcher $events, Session $session, Renderer $renderer, Validator $validator)
    {
        if(!isset($this->workflowTemplate)) throw new \Exception("Missing workflow template definition.");

        if(!isset($this->workflowHandlers)) throw new \Exception("Missing workflow handlers definition.");

        if(!isset($this->workflowForms)) throw new \Exception("Missing workflow forms definition.");

        if(!isset($this->workflowFields)) throw new \Exception("Missing workflow fields definition.");

        $this->events = $events;

        $this->session = $session;

        $this->renderer = $renderer;

        $this->validator = $validator;

        foreach($this->workflowHandlers as $subscriber){

            $this->events->subscribe($subscriber);

        }

        foreach($this->workflowFields as $type => $renderer){

            $this->renderer->addRenderer($type, $renderer);

        }
    }


    public function exists($workflow)
    {
        return isset($this->workflowForms[$workflow]);
    }

    public function resolve($workflow, $form, $params)
    {
        $class = $this->workflowForms[$workflow][$form];

        return new $class($params);
    }

    public function render(Workflow $workflow, $errors)
    {
        $fields = [];

        // sort fields by 'position'
        sort_children($workflow->fields);

        foreach($workflow->fields as $field => $data){

            $fields[] = $this->renderer->render($field, $data, $errors->get($field));

        }

        return view($this->workflowTemplate)
            ->with('options', $workflow->options)
            ->with('fields', $fields)
            ->render();
    }

    public function validateInput($fields, $request)
    {
        return $this->validator->run($fields, $request);
    }

    public function flashInput(array $fields)
    {
        return $this->session->flashInput($fields);
    }

    public function fireEvent(Workflow $workflow)
    {
        return $this->events->fire($workflow);
    }

    public function getForms($workflow)
    {
        $forms = $this->_getForms($workflow);

        return array_keys($forms);
    }

    protected function _getForms($workflow)
    {
        if($this->exists($workflow)){

            $forms = $this->workflowForms[$workflow];

            ksort($forms);

            return $forms;

        }

        throw new \Exception("Invalid workflow ".(string)$workflow);
    }

    public function getForm($workflow)
    {
        return $this->session->getForm($workflow);
    }

    public function setForm($workflow, $form)
    {
        return $this->session->setForm($workflow, $form);
    }

    public function getErrors($workflow)
    {
        return $this->session->getErrors($workflow);
    }

    public function setErrors($workflow, $errors)
    {
        return $this->session->setErrors($workflow, $errors);
    }


}