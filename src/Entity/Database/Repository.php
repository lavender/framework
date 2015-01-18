<?php
namespace Lavender\Entity\Database;

class Repository
{

    public $attributes;

    public function rows()
    {
        $columns = array_keys($this->attributes());

        return $this->entity->all($columns);
    }

    public function columns()
    {
        $labels = [];

        foreach($this->attributes() as $attribute => $config){

            $labels[] = isset($config['label']) ? $config['label'] : $attribute;

        }

        return $labels;
    }

    public function attributes()
    {
        if(!isset($this->attributes)){

            //make sure collection items have their id
            $this->attributes = array_merge(
                ['id' => ['label' => 'ID']],
                $this->entity->getConfig('attributes')
            );

        }

        return $this->attributes;
    }


    public function with($key, $value)
    {
        $this->$key = $value;
    }

}