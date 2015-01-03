<?php
namespace Lavender\Account\Listener;

class ForgotPassword
{

    /**
     * @param $data
     * @throws \Exception
     */
    public function handle($data)
    {
        if(!\Account::user()->forgotPassword($data['email'])){

            throw new \Exception(\Lang::get('account.alerts.wrong_password_forgot'));
        }

        \Message::addSuccess(\Lang::get('account.alerts.password_forgot'));
    }
}