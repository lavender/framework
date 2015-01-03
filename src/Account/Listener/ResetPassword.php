<?php
namespace Lavender\Account\Listener;

class ResetPassword
{
    /**
     * @param $data
     * @throws \Exception
     */
    public function handle($data)
    {

        if(!\Account::user()->resetPassword($data)){

            throw new \Exception(\Lang::get('account.alerts.wrong_password_reset'));
        }

        \Message::addSuccess(\Lang::get('account.alerts.password_reset'));
    }
}