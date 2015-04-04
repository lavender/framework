<?php
namespace Lavender\Form;

class Renderer
{

    protected $renderers;

    protected $resources;

    public function addRenderer($type, $renderer)
    {
        $this->renderers[$type] = $renderer;
    }

    public function addResource($type, $resource)
    {
        $this->resources[$type] = $resource;
    }

    /**
     * @return string
     */
    public function render($field, $data, $errors, $html = '')
    {
        //if null set default value
        $data['value'] = $this->defaultValue($data);

        // find or create field id
        $data['options']['id'] = $this->fieldId($field, $data);

        // set field name
        if(!isset($data['options']['name'])) $data['options']['name'] = $field;

        // set disabled if using default
        if($data['use_default'] && $data['value'] === null){

            $data['options']['disabled'] = true;

        }

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

        if($data['use_default']){

            $html .= $this->use_default(
                $data['use_default'],
                $field.'-use_default',
                $data['options']['id'],
                $data['value']
            );

        }

        // render the comment
        $html .= $this->comment($data['comment']);

        // render the errors
        $html .= $this->errors($data['validate'], $errors);

        return $html;
    }

    protected function _render($type, $value, $options = [], $resource = null)
    {
        list($renderer, $method) = $this->getClassMethod($this->renderers, $type, $type);

        if(is_string($resource)){

            list($resource, $resource_method) = $this->getClassMethod($this->resources, $resource, 'toArray');

            $resource = app($resource)->$resource_method();

        }

        return app($renderer)->$method($value, $options, $resource);
    }

    protected function getClassMethod($classes, $method, $default)
    {
        if(isset($classes[$method])){

            if(stristr($classes[$method], '@') === false){

                return [$classes[$method], $default];

            }

            return explode('@', $classes[$method]);

        }

        throw new \Exception("Undefined method \"{$method}\".");
    }

    protected function label($id, $label, $options = [])
    {
        if($label !== null){

            $options['for'] = $id;

            return $this->_render('label', $label, $options);
        }

        return '';
    }

    protected function use_default($label, $id, $parent_id, $value)
    {
        if(!is_string($label)) $label = "Use Default";

        $checkbox_options = [
            'id' => $id,
            'class' => 'field-use-default',
            'data-parent' => $parent_id,
        ];

        if($value === null){

            $checkbox_options['checked'] = true;

        }

        $html[] = $this->_render('checkbox', null, $checkbox_options);

        $html[] = $this->label($id, $label);

        return "<span class='use_default'>".implode(PHP_EOL, $html)."</span>";
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

    protected function defaultValue($data)
    {
        if($data['value'] === null){

            return $data['default'];

        }

        return $data['value'];
    }

    protected function fieldId($field, $data)
    {
        if(!isset($data['options']['id'])){

            return 'field-'.$field;

        }

        return $data['options']['id'];
    }
}
