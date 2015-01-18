<?php
namespace Lavender\Workflow\Services;

use Lavender\Support\Contracts\RendererInterface;
use Lavender\Support\Contracts\WorkflowInterface;

class Workflow implements WorkflowInterface
{
    /**
     * @var RendererInterface
     */
    protected $renderer;


    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Render the form
     * @return string
     * @throws \Exception
     * @todo handle exceptions better
     */
    public function render()
    {
        $config = \Workflow::resolve($this);

        $this->states = array_keys($config);

        $this->state = \Workflow::find($this);

        $this->fields = $config[$this->state]['fields'];

        $this->options = $config[$this->state]['options'];

        \Event::fire("workflow.{$this->workflow}.{$this->state}.before", [$this]);

        return $this->renderer->render($this);
    }

    /**
     * @param string $state
     * @param array $request
     */
    public function next($state, $request)
    {
        $config = \Workflow::resolve($this);

        $this->states = array_keys($config);

        $this->state = \Workflow::find($this);

        if($this->state == $state){

            // validate request
            \Workflow::validate($config[$state]['fields'], $request);

            // fire callbacks
            \Event::fire("workflow.{$this->workflow}.{$state}.after", [$request]);

            // set next state
            \Workflow::next($this);

        }
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

        throw new \BadMethodCallException("Method [$method] does not exist.");
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