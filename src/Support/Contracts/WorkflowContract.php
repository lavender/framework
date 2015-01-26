<?php
namespace Lavender\Support\Contracts;

interface WorkflowContract
{

    public function states($workflow);

    public function options($workflow, $state, $view);

}