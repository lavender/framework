<?php
namespace Lavender\Workflow\Facades;

use Illuminate\Support\Facades\Facade;

class Workflow extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'workflow.factory';
    }
}
