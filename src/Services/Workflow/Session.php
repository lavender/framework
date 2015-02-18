<?php
namespace Lavender\Services\Workflow;

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

    /**
     * @param string $workflow
     * @return mixed
     */
    public function getState($workflow)
    {
        return \Session::get("workflow.{$workflow}.state", false);
    }

    /**
     * @param string $workflow
     * @param string $state
     */
    public function setState($workflow, $state)
    {
        \Session::put("workflow.{$workflow}.state", $state);
    }

    public function setErrors($workflow, $errors)
    {
        \Session::put("workflow.{$workflow}.errors", $errors);
    }

    public function getErrors($workflow)
    {
        return \Session::pull("workflow.{$workflow}.errors", new MessageBag([]));
    }

}
