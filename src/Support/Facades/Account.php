<?php
namespace Lavender\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Account extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'account.service';
    }
}
