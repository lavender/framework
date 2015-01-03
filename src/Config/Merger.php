<?php
namespace Lavender\Config;

class Merger
{

    protected $merge = [];

    public function merge(array $configs)
    {
        foreach($configs as $config) $this->merge[] = $config;
    }

    public function getMerged()
    {
        return $this->merge;
    }
}