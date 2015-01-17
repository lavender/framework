<?php
namespace Lavender\Workflow\Contracts;

interface WorkflowContract
{

    public function states($workflow);

    public function options($workflow, $state);

}