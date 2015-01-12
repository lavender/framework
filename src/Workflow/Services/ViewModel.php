<?php
namespace Lavender\Workflow\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Lavender\Workflow\Contracts\ViewModelInterface;
use Lavender\Workflow\Contracts\RendererInterface;
use Lavender\Workflow\Exceptions\StateException;

class ViewModel implements ViewModelInterface
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    protected $workflow;

    protected $state;

    protected $config;

    protected $after;

    public $fields = [];

    public function __construct($workflow, $state, $config)
    {
        $this->workflow = $workflow;

        $this->state = $state;

        $this->config = $config;

        $this->after = $this->config['after'];

        $this->fields = $this->config['fields'];

        if($this->config['renderer']) $this->renderer = App::make($this->config['renderer']);
    }

    public function setDefaultRenderer(RendererInterface $renderer)
    {
        if(!isset($this->renderer)) $this->renderer = $renderer;
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




    public function handle($state, $request)
    {
        if($state != $this->state){

            // something went wrong
            throw new \InvalidArgumentException(
                sprintf(
                    "State requested \"%s\" does not match current state \"%s\" on workflow \"%s\"",
                    $state,
                    $this->state,
                    $this->workflow
                )
            );

        }

        // first we flash the input into session
        $this->flash();

        // validate the request
        $this->validate($request);

        // then we execute the after filters
        $this->after($request);
    }

    /**
     * Flash only fields where flash = true
     *
     * @param $fields
     */
    protected function flash()
    {
        $flash = array_where($this->fields, function($key, $config){

            return $config['flash'];

        });

        Input::flashOnly(array_keys($flash));
    }

    protected function validate($request)
    {
        $rules = [];

        foreach($this->fields as $field => $data){

            if($data['validate']) $rules[$field] = $data['validate'];

        }

        $validator = \Validator::make($request, $rules);

        if ($validator->fails()){

            throw new StateException(
                sprintf(
                    "Validator failed for workflow %s",
                    $this->workflow
                ),
                $validator
            );
        }
    }

    protected function after($request)
    {
        foreach($this->after as $after => $filter){

            if($model = new $filter['class']){

                $model->handle($request);

            }

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

    public function getWorkflow()
    {
        return $this->workflow;
    }

    public function getState()
    {
        return $this->state;
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