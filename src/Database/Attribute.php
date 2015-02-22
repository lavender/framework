<?php
namespace Lavender\Database;

use Lavender\Support\Contracts\EntityInterface;

class Attribute
{
    protected $entity;

    protected $key;

    public function __construct(EntityInterface $entity, $key)
    {
        $this->entity = $entity;

        $this->key = $key;
    }

    public function toLink($url = '/', $attrs = [])
    {
        return "<a href='{$url}'".attr($attrs).">" . $this->render() . "</a>";
    }

    public function toBold($attrs = [])
    {
        return "<strong".attr($attrs).">" . $this->render() . "</strong>";
    }


    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        if (is_numeric($key)) $key = $value;

        if ( ! is_null($value)) return $key.'="'.e($value).'"';
    }

    public function render()
    {
        return (string)$this->entity->{$this->key};
    }

    public function __toString()
    {
        return $this->render();
    }
}