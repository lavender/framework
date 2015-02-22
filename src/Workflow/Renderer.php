<?php
namespace Lavender\Workflow;

class Renderer
{

    protected $renderers;

    public function addRenderer($type, $renderer)
    {
        $this->renderers[$type] = $renderer;
    }

    /**
     * @return string
     */
    public function render($field, $data, $errors, $html = '')
    {
        //if null set default value
        $this->setDefaultValue($data);

        // find or create field id
        $this->setFieldId($field, $data);

        // set field name
        $data['options']['name'] = $field;

        // render the label
        $html .= $this->label(
            $data['options']['id'],
            $data['label'],
            $data['label_options']
        );

        // render the field
        $html .= $this->_render(
            $data['type'],
            $data['value'],
            $data['options'],
            $data['resource']
        );

        // render the comment
        $html .= $this->comment($data['comment']);

        // render the errors
        $html .= $this->errors($data['validate'], $errors);

        return $html;
    }

    protected function _render($type, $value, $options = [], $resource = null)
    {
        //todo assert/exception: field not found
        if(!isset($this->renderers[$type])) return '';

        list($class, $method) = $this->_renderer($type);

        $resolved = app($class);

        if($resource) $resource = app($resource);

        return $resolved->$method($value, $options, $resource);
    }

    protected function _renderer($type)
    {
        if(stristr($this->renderers[$type], '@') === false){

            return [$this->renderers[$type], $type];

        }

        return explode('@', $this->renderers[$type]);
    }

    protected function label($id, $label, $options)
    {
        if($label){

            $options['for'] = $id;

            return $this->_render('label', $label, $options);
        }

        return '';
    }

    protected function comment($comment)
    {
        if($comment) return $this->_render('comment', $comment);

        return '';
    }

    protected function errors($validate, $errors)
    {
        if($validate && $errors){

            return implode(PHP_EOL, array_build($errors, function ($key, $val){

                return [$key, $this->_render('error', $val)];

            }));

        }

        return '';
    }

    protected function setDefaultValue(&$data)
    {
        if($data['value'] === null){

            $data['value'] = $data['default'];

        }
    }

    protected function setFieldId($field, &$data)
    {
        if(!isset($data['options']['id'])){

            $data['options']['id'] = 'field-'.$field;

        }
    }
}
