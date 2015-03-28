<?php
namespace Lavender\Services;

use Illuminate\Http\Request;
use Lavender\Contracts\Workflow\Kernel;
use Lavender\Exceptions\WorkflowException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        return clone $this;
    }

    /**
     * Handle workflow form submission
     *
     * @param array|Request $request
     * @return mixed
     */
    public function handle(Request $request)
    {
        try{

            $workflow = $this->resolve();

            // flash input into session
            $this->kernel->flashInput($workflow->fields);

            // validate request
            $this->kernel->validateInput($workflow->fields, $request->all());

            // fire callbacks
            $this->kernel->fireEvent($workflow);

        } catch(WorkflowException $e){

            // workflow validation errors
            $this->kernel->setErrors($this->workflow, $e->getErrors()->messages());

        } catch(QueryException $e){

            // database errors
            //todo log exception
            throw new HttpException(500, $e->getMessage());
            //throw new HttpException(500, "Database error.");

        } catch(\Exception $e){

            // general exceptions
            //todo log exception
            throw new HttpException(500, $e->getMessage());

        }
    }

    /**
     * Render the current workflow
     * @return string
     */
    public function render()
    {
        try{
            $output = '';

            $workflow = $this->resolve();

            $errors = $this->kernel->getErrors($this->workflow);

            $output = $this->kernel->render($workflow, $errors);

        } catch(\Exception $e){

            // todo log exception
            $output = '<error>'.$e->getMessage().'</error>';

        }

        return $output;
    }


    protected function resolve()
    {
        return $this->kernel->resolve($this->workflow, $this->params);
    }


    public function getInstance()
    {
        return $this;
    }


    public function exists($workflow)
    {
        return $this->kernel->exists($workflow);
    }


    public function setParams(array $params)
    {
        if(!isset($this->params)) $this->params = (object)[];

        foreach($params as $k => $v) $this->params->$k = $v;
    }

    public function __toString()
    {
        return $this->render();
    }
}