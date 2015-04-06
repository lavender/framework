<?php
namespace Lavender\Services;

use Illuminate\Http\Request;
use Lavender\Contracts\Form\Kernel;
use Lavender\Exceptions\FormException;
use Illuminate\Database\QueryException;
use Lavender\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FormFactory
{

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var string name
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
     * Get the evaluated view contents for the given form.
     *
     * @param  string $form
     * @param array $params
     * @return $this
     */
    public function make($form, $params = [])
    {
        $this->form = $form;

        $this->setParams($params);

        return clone $this;
    }

    /**
     * Handle form form submission
     *
     * @param FormRequest $request
     * @return mixed
     */
    public function handle(FormRequest $request)
    {
        try{

            $form = $this->resolve();

            // validate request
            $request->validate($form->fields);

            $form->request = $request;

            // fire callbacks
            $this->kernel->fireEvent($form);

            // return success
            return true;

        } catch(FormException $e){

            // form validation errors
            $this->kernel->setErrors($this->form, $e->getErrors()->messages());

            // return failure
            return false;

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
     * Render the current form
     * @return string
     */
    public function render()
    {
        try{
            $output = '';

            $form = $this->resolve();

            $errors = $this->kernel->getErrors($this->form);

            $output = $this->kernel->render($form, $errors);

        } catch(\Exception $e){

            // todo log exception
            $output = '<error>'.$e->getMessage().'</error>';

        }

        return $output;
    }


    public function resolve()
    {
        return $this->kernel->resolve($this->form, $this->params);
    }


    public function getInstance()
    {
        return $this;
    }


    public function exists($form)
    {
        return $this->kernel->exists($form);
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