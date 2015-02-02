<?php
namespace Lavender\Support;

use Illuminate\Support\Facades\Redirect;
use Lavender\Support\Contracts\WorkflowContract;

abstract class Workflow implements WorkflowContract
{
    public function states()
    {
        return [];
    }

    public function template($state)
    {
        return 'workflow.form.container';
    }

    public function options($state, $params)
    {
        return [];
    }

    public function fields($state, $params)
    {
        return [];
    }

    public function response($state)
    {
        return Redirect::back();
    }
}