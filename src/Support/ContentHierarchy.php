<?php
namespace Lavender\Support;

abstract class ContentHierarchy
{
    protected $type;

    protected $array = [];


    public function make($type)
    {
        $this->type = $type;

        return $this;
    }

    public function add($group, array $element)
    {
        if(!isset($this->type)) throw new \Exception("Content type not set.");

        $this->array[$this->type][$group] = $this->toObject($element);

        return $this;
    }

    public function addTo($group, array $element)
    {
        if(!isset($this->type) || !isset($this->array[$this->type][$group])) throw new \Exception("Content group not set.");

        $this->array[$this->type][$group]->children[] = $this->toObject($element);

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

    protected function toObject($data)
    {
        $item = new \stdClass();

        $data = recursive_merge([
            'content' => null,
            'children' => [],
        ], $data);

        foreach($data as $key => $value){

            if($key == 'children' && $value){

                foreach($value as $index => $child){

                    $value[$index] = $this->toObject($child);

                }

            }

            $item->$key = $value;

        }

        return $item;
    }


    public function render()
    {
        try{

            if(!isset($this->layout)) throw new \Exception("Layout not found.");

            return view($this->layout)
                ->with('items', $this->all())->render();

        } catch (\Exception $e){

            // todo log exception
            return $e->getMessage();

        }
    }


    /**
     * Get the string contents of the content.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}