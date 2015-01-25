<?php
namespace Lavender\Support;

abstract class Shared
{
    protected $_data = [];

    abstract function __construct($optional = null);

    public function unsetData()
    {
        $this->_data = [];
    }

    public function addData($arr, $callback = null)
    {
        foreach($arr as $k => $v){

            $this->_data[$k] = $callback instanceof \Closure ? $callback($k) : $v;

        }
    }

    public function __get($key)
    {
        return $this->_data[$key];
    }

    public function __set($key, $value)
    {
        //todo are you sure?
        return $this->_data[$key] = $value;
    }

}