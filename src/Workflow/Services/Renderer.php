<?php
namespace Lavender\Workflow\Services;

use Illuminate\View\Factory;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Contracts\ArrayableInterface as Arrayable;
use Lavender\Workflow\Interfaces\RendererInterface;

class Renderer implements RendererInterface
{
    protected $view;
    protected $workflow;
    protected $state;
    protected $config;

    public function __construct(Factory $factory)
    {
        $this->view = $factory;
    }

    public function make($workflow, $state, $config)
    {
        $this->workflow = $workflow;

        $this->state = $state;

        $this->config = $config;
    }

    protected function formId()
    {
        return 'form-'.$this->workflow.'-'.$this->state;
    }

    protected function formAction()
    {
        return \URL::to(\Config::get('store.workflow_base_url').'/'.$this->workflow.'/'.$this->state);
    }

    public function render()
    {
        // load our state config into a transport object
        $transport = $this->transport();

        // call before filters
        if(isset($this->config['before'])) $this->handleBefore($this->config['before'], $transport);

        $container = $this->view->make($transport->layout);

        // return rendered form fields
        if($transport->fields){

            $groups = $this->renderFields($transport->fields);

            $container->withGroups($groups);

        }

        // return the view container w/ options and errors
        return $container->withOptions($transport->options)
            ->withErrors($this->hasError('form'));
    }

    protected function transport()
    {
        $transport = new Transport;

        // options passed to Form:open()
        $transport->options = [
            'method' => 'post',
            'id' => $this->formId(),
            'url' => $this->formAction(),
        ];

        // prepare fields
        $transport->fields = $this->config['fields'];

        // prepare layout
        $transport->layout = $this->config['layout'];

        return $transport;
    }

    protected function renderFields($fields)
    {
        // todo something with workflow/success
        //\Message::addSuccess("win");

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
                    $html .= \Form::$field['type']($name, $field['value'], $field['options']);
                    break;

                // Button fields: $value, $options
                case 'reset':
                case 'submit':
                case 'button':
                    $html .= \Form::$field['type']($field['value'], $field['options']);
                    break;

                // Protected fields: $name, $options
                case 'file':
                    $form_options['files'] = true;
                case 'password':
                    $html .= \Form::$field['type']($name, $field['options']);
                    break;

                // Select fields: $name, $list, $selected, $options
                case 'select':
                    $html .= \Form::select($name, $field['values'], $field['value'], $field['options']);
                    break;

                // Checkable fields: $name, $value, $checked, $options
                case 'checkbox':
                    $field['value']?:1;
                case 'radio':
                    $html .= \Form::$field['type']($name, $field['value'], $field['checked'], $field['options']);
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

        //todo support groups
        return ['default' => $rendered];
    }

    protected function handleBefore($filters, &$transport)
    {
        foreach($filters as $before => $filter){

            if($model = new $filter['class']) $model->handle($transport);

        }
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
