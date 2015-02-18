<?php
namespace Lavender\Services\Workflow;

use Form;
use Illuminate\Support\MessageBag;

class Renderer
{

    protected $errors;

    /**
     * @return string
     */
    public function render($workflow, MessageBag $errors)
    {
        $this->errors = $errors;

        $fields = $this->renderFields($workflow->fields);

        return view($workflow->template)
            ->with('options', $workflow->options)
            ->with('fields', $fields)
            ->render();
    }


    protected function renderFields(array $fields)
    {
        $rendered = [];

        // sort fields by 'position'
        sort_children($fields);

        // render payload fields
        foreach($fields as $name => $field){

            //todo: if null, set default value
            $field['value'] = $field['value'] === null ? '' : $field['value'];

            // find or create field id
            $field['options']['id'] = isset($field['options']['id']) ? $field['options']['id'] : 'field-'.$name;

            // start the payload output string
            $html = '';

            // create the label
            if($field['label']) $html .= Form::label($field['options']['id'], $field['label'], $field['label_options']);

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

                // Tables: $name, $collection, $selected, $options
                case 'table':
                    $html .= Form::table($field['values'], $field['headers']);
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

            if($field['validate'] && $error = $this->getError($name)){

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

    protected function getError($key)
    {
        return $this->errors->get($key);
    }

}
