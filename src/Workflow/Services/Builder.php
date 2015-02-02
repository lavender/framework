<?php
namespace Lavender\Workflow\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Lavender\Support\Contracts\WorkflowContract;

class Builder
{

    /**
     * Params passed to workflow when created
     * @var array
     */
    protected $params;

    /**
     * All workflow classes
     * @var array
     */
    protected $classes;

    /**
     * Resolved data.
     * @var array
     */
    protected $_data = [];

    protected $defaults = [
        // field label (optional)
        'label' => null,
        'label_options' => [],

        // applies to all fields
        'type' => 'text',
        'position' => 0,
        'name' => null,
        'value' => null,
        'options' => ['id' => null],
        'validate' => [],
        'comment' => null,
        'flash' => true,

        //applies to select fields and tables
        'values' => [],

        //applies to tables
        'headers' => [],

        //applies to checkbox & radio fields
        'checked' => [],
    ];

    /**
     * @param string $workflow name
     * @param array $classes
     * @param array $params
     */
    public function __construct($workflow, array $classes, array $params)
    {
        $this->workflow = $workflow;

        $this->resolveClasses($classes);

        $this->params = $params;
    }

    /**
     * Handle workflow form submission
     * @param $state
     * @param $request
     * @param null $errors
     * @return mixed
     */
    public function post($state, $request, $errors = null)
    {
        $response = Redirect::back();

        try{

            if($state == $this->getState()){

                // load fields
                $fields = $this->getFields($state);

                // flash input into session
                $this->getSession()->flash($fields);

                // validate request
                $this->getValidator()->run($fields, $request);

                // fire callbacks
                Event::fire("workflow.{$this->workflow}.{$state}.after", [$request]);

                // all states
                $states = $this->getStates();

                // current state index
                $curr = array_search($state, $states);

                // if next index available, set new state
                if(isset($states[$curr + 1])){

                    $state = $states[$curr + 1];

                    $this->getSession()->set($this->workflow, $state);

                } else{

                    // end of workflow, remain on last step

                }

                //todo return state response
                $response = $this->getResponse($state);

            } else {

                //todo log failure
                throw new \Exception("Unknown state.");

            }

//        } catch(StateException $e){
//
//            $errors = $e->getErrors()->messages();
//
//        } catch(QueryException $e){
//
//            //todo log exception
//            $errors = ["Database error."];

        } catch(\Exception $e){

            //todo log exception
            $errors = ['<pre>',$e->getMessage(), $e->getTraceAsString()];

        }

        if($errors){


            print_r($errors);die;
            $response->withErrors($errors, $this->workflow . '_' . $state);
        }

        return $response;
    }

    /**
     * Render the current workflow
     * @param string $output
     * @return string
     */
    public function render($output = '')
    {
        try{

            if($state = $this->getState()){

                $template = $this->getTemplate($state);

                $options = $this->getOptions($state);

                $fields = $this->getFields($state);

                $identity = $this->workflow.'_'.$state;

                $output = $this->getRenderer([$template, $options, $fields, $identity])->render();
            }

        } catch(\Exception $e){

            // todo log exception
            $output = "Error rendering workflow: ".$e->getMessage()."<pre>".$e->getTraceAsString();

        }

        return $output;
    }

    protected function resolveClasses($classes)
    {
        foreach($classes as $priority => $class){

            $class = new $class();

            if($class instanceof WorkflowContract){

                $this->classes[] = $class;

            } else {

                // todo log failure

            }

        }
    }

    /**
     * Collect the options that are passed to Form:open()
     * @param $state
     * @return mixed
     */
    protected function getOptions($state)
    {
        if(!isset($this->_data['options'])){

            $this->_data['options'] = [];

            /** @var WorkflowContract $class */
            foreach($this->classes as $class){

                if($options = $class->options($state, $this->params)){

                    $this->_data['options'] = recursive_merge(
                        $this->_data['options'],
                        $options
                    );

                }

            }

        }

        return $this->_data['options'];
    }

    /**
     * Collect the fields for a given state
     * @param $state
     * @return mixed
     */
    protected function getFields($state)
    {
        if(!isset($this->_data['fields'])){

            $this->_data['fields'] = [];

            /** @var WorkflowContract $class */
            foreach($this->classes as $class){

                if($fields = $class->fields($state, $this->params)){

                    //Event::fire("workflow.{$this->workflow}.{$state}.before", $this->params);

                    // collect fields and merge defaults
                    foreach($fields as $key => $data){

                        $fields[$key] = recursive_merge(
                            $this->defaults,
                            $data
                        );

                    }

                    $this->_data['fields'] = recursive_merge(
                        $this->_data['fields'],
                        $fields
                    );

                }

            }

        }

        return $this->_data['fields'];
    }

    protected function getTemplate($state)
    {
        if(!isset($this->_data['template'])){

            /** @var WorkflowContract $class */
            foreach($this->classes as $class){

                if($template = $class->template($state)){

                    // template used to render the form
                    $this->_data['template'] = $template;

                    //todo break or allow override?

                }

            }

        }

        return $this->_data['template'];
    }

    protected function getResponse($state)
    {
        if(!isset($this->_data['response'])){

            /** @var WorkflowContract $class */
            foreach($this->classes as $class){

                if($response = $class->response($state)){

                    // response sent after successful submission
                    $this->_data['response'] = $response;

                    //todo break or allow override?

                }

            }

        }

        return $this->_data['response'];
    }

    protected function getStates()
    {
        if(!isset($this->_data['states'])){

            $this->_data['states'] = [];

            foreach($this->classes as $class){

                $this->_data['states'] = array_merge(
                    $this->_data['states'],
                    $class->states()
                );

            }

        }

        return $this->_data['states'];
    }

    protected function getState()
    {
        // all states
        $states = $this->getStates();

        // current or default state
        $default_state = reset($states);

        $state = $this->getSession()->find($this->workflow, $default_state);

        // current state index
        $curr = array_search($state, $states);

        // unknown state - todo log failure
        if($curr === false) return false;

        return $state;
    }

    public function getSession()
    {
        return App::make('workflow.session');
    }

    public function getRenderer($params)
    {
        return App::make('workflow.renderer', $params);
    }

    public function getValidator()
    {
        return App::make('workflow.validator');
    }

    public function __toString()
    {
        return $this->render();
    }
}
