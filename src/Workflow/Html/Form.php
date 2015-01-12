<?php
namespace Lavender\Workflow\Html;

class Form
{
    public $layout = 'workflow.form.container';

    public $options = [];

    public $fields = [];

    /**
     * Instantiate a new workflow object
     *
     * @param RepositoryInterface $repo
     * @param RendererInterface $renderer
     * @throws \InvalidArgumentException
     */
    public function __construct(RepositoryInterface $repo, RendererInterface $renderer)
    {
        $this->renderer = $renderer;

        $this->repo = $repo;

        $this->repo->model($this);
    }

    /**
     * Render the form
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        if(!isset($this->workflow)) throw new \Exception('Invalid instantiation of '.get_class($this));

        //todo handle workflow
        \Event::fire('workflow.'.$this->workflow, [$this]);


        return \View::make($this->layout)
            ->with('options', $this->options)
            ->with('fields', $this->fields)
            ->render();
    }

    /**
     * Add a piece of data to the form.
     *
     * @param  string|array $key
     * @param  mixed $value
     * @return $this
     */
    public function with($key, $value)
    {
        $this->$key = $value;

        return $this;
    }


    /**
     * Dynamically bind parameters to the form.
     *
     * @param  string $method
     * @param  array $parameters
     * @return $this
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if(starts_with($method, 'with')){
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }

        throw new \BadMethodCallException("Method [$method] does not exist on workflow.");
    }


    /**
     * Get the string contents of the form.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}