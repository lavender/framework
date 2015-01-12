<?php namespace Lavender\View\Services;

class MenuBuilder
{
    protected $type = 'frontend';
    protected $menu = [
        'frontend' => [],
        'backend' => [],
    ];

    public function frontend()
    {
        $this->type = 'frontend';

        return $this;
    }

    public function backend()
    {
        $this->type = 'backend';

        return $this;
    }

    public function add($group, array $element)
    {
        $this->menu[$this->type][$group] = $this->make($element);

        return $this;
    }

    public function addTo($group, array $element)
    {
        $this->menu[$this->type][$group]->children[] = $this->make($element);

        return $this;
    }

    public function remove($group)
    {
        unset($this->menu[$this->type][$group]);

        return $this;
    }

    public function get($group = null)
    {
        if($group) return $this->menu[$this->type][$group];

        return $this->menu[$this->type];
    }

    public function all()
    {
        return $this->get();
    }

    protected function make($data)
    {
        $item = new \stdClass();

        $data = recursive_merge([
            'content' => null,
            'children' => [],
        ], $data);

        foreach($data as $k => $v){

            if($k == 'children' && $v){

                array_walk($v, function (&$v){
                    $v = $this->make($v);
                });

            }

            $item->$k = $v;

        }

        return $item;
    }
}
