<?php
namespace Lavender\Core\Services;

class Installer
{

    protected $callbacks;

    public function install($key, $callback)
    {
        $this->callbacks['install'][$key] = $callback;
    }

    public function update($key, $callback)
    {
        $this->callbacks['update'][$key] = $callback;
    }

    public function installs()
    {
        return $this->callbacks['install'];
    }

    public function updates()
    {
        return $this->callbacks['update'];
    }
}