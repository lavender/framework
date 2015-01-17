<?php
namespace Lavender\Workflow\Services;

use Lavender\Support\Contracts\ViewModelInterface;
use Lavender\Support\Contracts\RendererInterface;

class ViewModel implements ViewModelInterface
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
     */
    public function render()
    {
        return $this->renderer->render($this);
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