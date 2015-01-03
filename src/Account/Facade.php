<?php
namespace Lavender\Account;

use Illuminate\Support\Facades\Facade as CoreFacade;

class Facade extends CoreFacade
{

    protected static function getFacadeAccessor()
    {
        return 'account.service';
    }
}
