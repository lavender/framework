<?php
namespace Lavender\Contracts\Form;

use Lavender\Contracts\Form;

interface Kernel
{
    public function exists($form);

    public function resolve($form, $params);

    public function render(Form $form, $errors);

    public function validateInput($fields, $request);

    public function flashInput(array $fields);

    public function fireEvent(Form $form);

    public function getErrors($form);

    public function setErrors($form, $errors);

    public function getForms();

}