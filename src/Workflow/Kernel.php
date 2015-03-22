<?php
namespace Lavender\Workflow;

use Illuminate\Contracts\Events\Dispatcher;
use Lavender\Contracts\Workflow;
use Lavender\Contracts\Workflow\Kernel as WorkflowKernel;

abstract class Kernel implements WorkflowKernel
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
    protected $forms = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @var array
     */
    protected $resources = [];

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
        $this->events       = $events;

        $this->session      = $session;

        $this->renderer     = $renderer;

        $this->validator    = $validator;

        $this->register();
    }

    public function exists($workflow)
    {
        return isset($this->forms[$workflow]);
    }

    public function resolve($workflow, $params)
    {
        $class = $this->forms[$workflow];

        return app()->make($class, [$params]);
    }

    public function render(Workflow $workflow, $errors)
    {
        $fields = [];

        // sort fields by 'position'
        sort_children($workflow->fields);

        foreach($workflow->fields as $field => $data){

            $fields[] = $this->renderer->render($field, $data, $errors->get($field));

        }

        return view($workflow->template ? : $this->template)
            ->with('options', $workflow->options)
            ->with('fields', $fields)
            ->render();
    }

    public function fireEvent(Workflow $workflow)
    {
        return $this->events->fire($workflow);
    }

    public function validateInput($fields, $request)
    {
        return $this->validator->run($fields, $request);
    }

    public function flashInput(array $fields)
    {
        return $this->session->flashInput($fields);
    }

    public function getErrors($workflow)
    {
        return $this->session->getErrors($workflow);
    }

    public function setErrors($workflow, $errors)
    {
        return $this->session->setErrors($workflow, $errors);
    }

    protected function register()
    {
        foreach($this->fields as $type => $renderer){

            $this->renderer->addRenderer($type, $renderer);

        }

        foreach($this->resources as $type => $resource){

            $this->renderer->addResource($type, $resource);

        }

        foreach($this->handlers as $subscriber){

            $this->events->subscribe($subscriber);

        }
    }

}