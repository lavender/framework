<?php
namespace Lavender\Support\Contracts;

interface WorkflowContract
{

    public function states();

    public function options($state, $params);

    public function template($state);

    public function fields($state, $params);

    public function response($state);

}