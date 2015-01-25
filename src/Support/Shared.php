<?php
namespace Lavender\Support;

abstract class Shared
{
    protected $_data = [];

    public function unsetData()
    {
        $this->_data = [];
    }

    public function addData($arr, $callback = null)
    {
        foreach($arr as $k => $v){

            $this->_data[$k] = $callback instanceof \Closure ? $callback($k, $v) : $v;

        }
    }

    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function __set($key, $value)
    {
        //todo are you sure?
        return $this->_data[$key] = $value;
    }

}