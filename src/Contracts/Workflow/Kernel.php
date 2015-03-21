<?php
namespace Lavender\Contracts\Workflow;

use Lavender\Contracts\Workflow;
use Symfony\Component\HttpFoundation\Request;

interface Kernel
{
    public function exists($workflow);

    public function resolve($workflow, $params);

    public function render(Workflow $workflow, $errors);

    public function validateInput($fields, $request);

    public function flashInput(array $fields);

    public function fireEvent(Workflow $workflow);

    public function getErrors($workflow);

    public function setErrors($workflow, $errors);

}