<?php
namespace Lavender\Workflow\Services;

use Illuminate\Support\Facades\Form;
use Illuminate\View\Factory as View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Contracts\ArrayableInterface as Arrayable;
use Lavender\Support\Contracts\RendererInterface;
use Lavender\Support\Contracts\WorkflowInterface;

class Renderer implements RendererInterface
{
    /**
     * @var string form container template
     */
    protected $container = 'workflow.form.container';

    /**
     * @var View
     */
    protected $view;


    /**
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * @param WorkflowInterface $workflow
     * @return mixed
     */
    public function render(WorkflowInterface $workflow)
    {
//        try{
            $this->workflow = $workflow->workflow;

            $this->state = $workflow->state;

            $options = $workflow->options;

            $fields = $this->renderFields($workflow->fields);

//        }catch (\Exception $e){
//
//            // todo log exception
//            return $e->getMessage();
//
//        }
        return $this->view->make($this->container)
            ->with('options', $options)
            ->with('fields', $fields)
            ->render();
    }

    protected function renderFields($fields)
    {
        $rendered = [];

        // sort fields by 'position'
        sort_children($fields);

        // render payload fields
        foreach($fields as $name => $field){

            // todo: if 'values' represents a source model, request the values
            if($field['values'] instanceof Arrayable) $field['values'] = new $field['values']->toArray();

            //todo: if null, set default value
            $field['value'] = $field['value'] === null ? '' : $field['value'];

            // find or create field id
            $field['options']['id'] = $field['options']['id']?:$name;

            // start the payload output string
            $html = '';

            // create the label
            if($field['label']) $html .= \Form::label($field['options']['id'], $field['label'], $field['label_options']);

            // create the field
            switch($field['type']){

                // Text fields: $name, $value, $options
                case 'textarea':
                    // laravel sets cols/rows by default, not responsive-friendly
                    if(!isset($field['options']['cols']) && !isset($field['options']['rows'])){
                        $field['options']['cols']=null;
                        $field['options']['rows']=null;
                    }
                case 'text':
                case 'hidden':
                case 'email':
                case 'url':
                case 'number':
                    $html .= Form::$field['type']($name, $field['value'], $field['options']);
                    break;

                // Button fields: $value, $options
                case 'reset':
                case 'submit':
                case 'button':
                    $html .= Form::$field['type']($field['value'], $field['options']);
                    break;

                // Protected fields: $name, $options
                case 'file':
                    $form_options['files'] = true;
                case 'password':
                    $html .= Form::$field['type']($name, $field['options']);
                    break;

                // Select fields: $name, $list, $selected, $options
                case 'select':
                    $html .= Form::select($name, $field['values'], $field['value'], $field['options']);
                    break;

                // Checkable fields: $name, $value, $checked, $options
                case 'checkbox':
                    $field['value']?:1;
                case 'radio':
                    $html .= Form::$field['type']($name, $field['value'], $field['checked'], $field['options']);
                    break;

                // todo implement these also:
                //image($url, $name = null, $attributes = array())
                //selectRange($name, $begin, $end, $selected = null, $options = array())
                //selectYear($name, $begin, $end, $selected = null, $options = array())
                //selectMonth($name, $selected = null, $options = array(), $format = '%B')

                default:
                    throw new \InvalidArgumentException(sprintf("Invalid field type \"%s\".", $field['type']));
                    break;
            }

            if($field['comment']){

                $html .= '<span class="form-comment">'.$field['comment'].'</span>';

            }

            if($field['validate'] && $error = $this->hasError($name)){

                $html .= implode(PHP_EOL, array_build($error, function($key, $val){

                    return [$key, '<span class="form-error">'.$val.'</span>'];

                }));

            }

            // add the output string
            $class = 'field-'.$field['options']['id'];
            $rendered[$class] = $html;

        }

        return $rendered;
    }

    protected function hasError($name = null)
    {
        if($errors = \Session::get('errors')){

            if($errors instanceof ViewErrorBag){

                $key = "{$this->workflow}_{$this->state}";

                if($name == null) return $errors->$key->all();

                return $errors->$key->get($name);

            }

            //todo just checking..
            die("INVALID ERRORS PASSED");

        }

        return [];
    }

}
