<?php
namespace Lavender\Core\Services;

class Bootloader
{
    protected $callbacks;

    public function booting($type, $model)
    {
        $callbacks = isset($this->callbacks[$type]) ?
            $this->callbacks[$type] : [];

        foreach($callbacks as $callback){

            $callback($model);

        }
    }

    public function booted($type, \Closure $callback)
    {
        $this->callbacks[$type][] = $callback;
    }
}