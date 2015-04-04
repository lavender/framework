<?php
namespace Lavender\Form;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;

class Session
{

    /**
     * Flash only fields where flash = true
     *
     * @param array $fields
     */
    public function flashInput(array $fields)
    {
        $flash = array_where($fields, function($key, $config){

            return $config['flash'];

        });

        Input::flashOnly(array_keys($flash));
    }

    public function setErrors($form, $errors)
    {
        \Session::put("form.{$form}.errors", $errors);
    }

    public function getErrors($form)
    {
        return \Session::pull("form.{$form}.errors", new MessageBag([]));
    }

}
