<?php
namespace Lavender\View\Facades;

use Illuminate\Support\Facades\Facade;

class Message extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'message.service';
    }
}
