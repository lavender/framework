<?php
namespace Lavender\Core\Services;

class Installer
{

    protected $callbacks;

    public function install($key, $callback, $priority = 10)
    {
        $this->callbacks['install'][$key] = ['callback' => $callback, 'position' => $priority];
    }

    public function update($key, $callback, $priority = 10)
    {
        $this->callbacks['update'][$key] = ['callback' => $callback, 'position' => $priority];
    }

    public function installs()
    {
        return $this->callbacks('install');
    }

    public function updates()
    {
        return $this->callbacks('update');
    }

    protected function callbacks($type)
    {
        $callbacks = $this->callbacks[$type];

        sort_children($callbacks);

        foreach($callbacks as $key => $callback) $callbacks[$key] = $callback['callback'];

        return $callbacks;
    }
}