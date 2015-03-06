<?php
namespace Lavender\Services;

use Lavender\Contracts\Workflow\Kernel;
use Lavender\Exceptions\WorkflowException;
use Illuminate\Database\QueryException;

class WorkflowFactory
{

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var string name
     */
    protected $workflow;

    /**
     * @var string|integer id
     */
    protected $form;

    /**
     * @var \stdClass
     */
    protected $params;

    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
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

        $this->setParams($params);

        $this->loadForm();

        return clone $this;
    }

    /**
     * Handle workflow form submission
     *
     * @param array $request
     * @return mixed
     * @throws \Exception
     */
    public function handle(array $request)
    {
        try{

            unset($request['_token']);

            $workflow = $this->resolve(['request' => $request]);

            // flash input into session
            $this->kernel->flashInput($workflow->fields);

            // validate request
            $this->kernel->validateInput($workflow->fields, $request);

            // fire callbacks
            $this->kernel->fireEvent($workflow);

            // go to next form
            $this->nextForm();

        } catch(WorkflowException $e){

            // workflow validation errors
            $this->kernel->setErrors($this->workflow, $e->getErrors()->messages());

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
    public function render($params = [])
    {
        $output = '';

        try{

            $workflow = $this->resolve($params);

            $errors = $this->kernel->getErrors($this->workflow);

            $output = $this->kernel->render($workflow, $errors);

        } catch(\Exception $e){

            // todo log exception
            $output = $e->getMessage().'<pre>'.$e->getTraceAsString().'</pre>';

        }

        return $output;
    }


    protected function resolve($params = [])
    {
        $resolved = $this->kernel->resolve($this->workflow, $this->form, $this->params);

        foreach($params as $k => $v) $resolved->$k = $v;

        return $resolved;
    }


    public function getInstance()
    {
        return $this;
    }


    public function setCurrentForm($form)
    {
        $forms = $this->kernel->getForms($this->workflow, true);

        $form = array_search($form, $forms);

        if($form !== false){

            $this->form = $form;

            $this->updateSession();

            return;

        }

        throw new \Exception("Invalid form ".(string)$form);
    }


    public function isCurrentForm($form)
    {
        return $form == $this->kernel->getFormClass($this->workflow, $this->form);
    }


    public function exists($workflow)
    {
        return $this->kernel->exists($workflow);
    }


    public function setParams(array $params)
    {
        if(!isset($this->params)) $this->params = new \stdClass();

        foreach($params as $k => $v) $this->params->$k = $v;
    }


    public function reset()
    {
        $forms = $this->kernel->getForms($this->workflow);

        $this->form = reset($forms);

        $this->updateSession();
    }


    protected function nextForm()
    {
        $forms = $this->kernel->getForms($this->workflow);

        $curr = array_search($this->form, $forms);

        if($form = isset($forms[$curr + 1]) ? $forms[$curr + 1] : false){

            $this->form = $form;

            $this->updateSession();

        }
    }

    protected function loadForm()
    {
        $this->form = $this->kernel->getForm($this->workflow);

        if($this->form === false){

            $forms = $this->kernel->getForms($this->workflow);

            $this->form = reset($forms);

            $this->updateSession();
        }
    }


    protected function updateSession()
    {
        $this->kernel->setForm($this->workflow, $this->form);
    }


    public function __toString()
    {
        return $this->render();
    }
}