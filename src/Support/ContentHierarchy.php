<?php
namespace Lavender\Support;

use Illuminate\Support\Facades\View;
use Lavender\Support\Facades\Message;

abstract class ContentHierarchy
{
    protected $type;

    protected $array = [];

    protected $allowed_types = [];

    public function make($type)
    {
        if(in_array($type, $this->allowed_types)){

            $this->type = $type;

        }

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
        $view = '';

        try{

            if(!isset($this->layout)) throw new \Exception("Layout not found.");

            $view = View::make($this->layout)
                ->with('items', $this->all())->render();

        } catch (\Exception $e){

            //todo log getTraceAsString
            Message::addError("[ContentHierarchy] ".$e->getMessage());

        }

        return $view;
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