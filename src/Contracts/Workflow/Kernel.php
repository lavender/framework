<?php
namespace Lavender\Contracts\Workflow;

use Lavender\Contracts\Workflow;

interface Kernel
{
    public function exists($workflow);

    public function resolve($workflow, $form, $params);

    public function render(Workflow $workflow, $errors);

    public function validateInput($fields, $request);

    public function flashInput(array $fields);

    public function fireEvent(Workflow $workflow);

    public function getForms($workflow);

    public function getForm($workflow);

    public function setForm($workflow, $form);

    public function getErrors($workflow);

    public function setErrors($workflow, $errors);

}