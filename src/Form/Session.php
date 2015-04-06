<?php
namespace Lavender\Form;

use Illuminate\Support\MessageBag;

class Session
{

    public function setErrors($form, $errors)
    {
        \Session::put("form.{$form}.errors", $errors);
    }

    public function getErrors($form)
    {
        return \Session::pull("form.{$form}.errors", new MessageBag([]));
    }

}
