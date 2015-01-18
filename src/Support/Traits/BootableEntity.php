<?php
namespace Lavender\Support\Traits;

trait BootableEntity
{

    public function booting($model)
    {
        app('bootloader')->booting($this->entity, $model);
    }

    public function booted(\Closure $callback)
    {
        app('bootloader')->booted($this->entity, $callback);
    }

}