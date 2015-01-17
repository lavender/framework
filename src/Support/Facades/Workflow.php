<?php
namespace Lavender\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Workflow extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'workflow.factory';
    }
}
