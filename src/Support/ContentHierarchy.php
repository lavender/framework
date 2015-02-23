<?php
namespace Lavender\Support;

abstract class ContentHierarchy
{
    protected $type;

    protected $array = [];

    abstract public function view();

    abstract public function prepare($data);

    public function make($type)
    {
        $this->type = $type;

        return $this;
    }

    public function add($group, array $element)
    {
        if(!isset($this->type)) throw new \Exception("Content type not set.");

        $this->array[$this->type][$group] = $this->prepare($element);

        return $this;
    }

    public function addTo($group, array $element)
    {
        if(!isset($this->type) || !isset($this->array[$this->type][$group])) throw new \Exception("Content group not set.");

        $this->array[$this->type][$group]->children[] = $this->prepare($element);

        return $this;
    }

    public function remove($group)
    {
        if(!isset($this->type) || !isset($this->array[$this->type][$group])) throw new \Exception("Content group not set.");

        unset($this->array[$this->type][$group]);

        return $this;
    }

    public function get($group = null)
    {
        if(!isset($this->type)) throw new \Exception("Content type not set.");

        if(!isset($this->array[$this->type])) return [];

        if($group) return $this->array[$this->type][$group];

        return $this->array[$this->type];
    }

    public function all()
    {
        return $this->get();
    }


    /**
     * Get the string contents of the content.
     *
     * @return string
     */
    public function __toString()
    {
        try{

            return $this->view()
                ->with('items', $this->all())
                ->render();

        } catch (\Exception $e){

            // todo log exception
            return $e->getMessage();

        }
    }

}