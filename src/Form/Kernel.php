<?php
namespace Lavender\Form;

use Illuminate\Contracts\Events\Dispatcher;
use Lavender\Contracts\Form;
use Lavender\Contracts\Form\Kernel as FormKernel;

abstract class Kernel implements FormKernel
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
     * Initialize the form kernel.
     *
     * @param Dispatcher $events
     * @param Session $session
     * @param Renderer $renderer
     * @throws \Exception
     */
    public function __construct(Dispatcher $events, Session $session, Renderer $renderer)
    {
        $this->events       = $events;

        $this->session      = $session;

        $this->renderer     = $renderer;

        $this->register();
    }

    public function exists($form)
    {
        return isset($this->forms[$form]);
    }

    public function resolve($form, $params)
    {
        $class = $this->forms[$form];

        return app()->make($class, [$params]);
    }

    public function render(Form $form, $errors)
    {
        $fields = [];

        // sort fields by 'position'
        sort_children($form->fields);

        foreach($form->fields as $field => $data){

            $fields[] = $this->renderer->render($field, $data, $errors->get($field));

        }

        return view($form->template ? : $this->template)
            ->with('options', $form->options)
            ->with('fields', $fields)
            ->render();
    }

    public function fireEvent(Form $form)
    {
        return $this->events->fire($form);
    }

    public function all()
    {
        return $this->forms;
    }

    public function getErrors($form)
    {
        return $this->session->getErrors($form);
    }

    public function setErrors($form, $errors)
    {
        return $this->session->setErrors($form, $errors);
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