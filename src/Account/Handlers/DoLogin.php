<?php
namespace Lavender\Account\Handlers;

class DoLogin
{

    public function handle($data)
    {
        if(!\Account::user()->logAttempt($data, \Config::get('store.signup_confirm'))){

            if(\Account::user()->isThrottled($data)){

                throw new \Exception(\Lang::get('account.alerts.too_many_attempts'));
            } elseif(\Account::user()->existsButNotConfirmed($data)){

                throw new \Exception(\Lang::get('account.alerts.instructions_sent'));
            }

            throw new \Exception(\Lang::get('account.alerts.wrong_credentials'));
        }
    }
}