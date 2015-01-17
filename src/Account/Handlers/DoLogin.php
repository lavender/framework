<?php
namespace Lavender\Account\Handlers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Lavender\Account\Facades\Account;
use Lavender\Workflow\Facades\Workflow;

class DoLogin
{

    public function handle($request)
    {
        if(!Account::user()->logAttempt($request, Config::get('store.signup_confirm'))){

            if(Account::user()->isThrottled($request)){

                throw new \Exception(Lang::get('account.alerts.too_many_attempts'));

            } elseif(Account::user()->existsButNotConfirmed($request)){

                throw new \Exception(Lang::get('account.alerts.instructions_sent'));

            }

            throw new \Exception(Lang::get('account.alerts.wrong_credentials'));

        }

        Workflow::redirect('account/dashboard');
    }
}