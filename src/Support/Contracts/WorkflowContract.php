<?php
namespace Lavender\Support\Contracts;

interface WorkflowContract
{

    public function states();

    public function options($state);

    public function template($state);

}