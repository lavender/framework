<?php
namespace Lavender\Account\Handlers;

use Illuminate\Support\Facades\Lang;
use Lavender\Account\Facades\Account;
use Lavender\View\Facades\Message;
use Lavender\Workflow\Facades\Workflow;

class ForgotPassword
{

    /**
     * @param $data
     * @throws \Exception
     */
    public function handle($data)
    {
        if(!Account::user()->forgotPassword($data['email'])){

            throw new \Exception(Lang::get('account.alerts.wrong_password_forgot'));
        }

        Message::addSuccess(Lang::get('account.alerts.password_forgot'));

        Workflow::redirect('account/login');
    }
}