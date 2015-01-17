<?php
namespace Lavender\Account\Handlers;


use Illuminate\Support\Facades\Lang;
use Lavender\Account\Facades\Account;
use Lavender\View\Facades\Message;
use Lavender\Workflow\Facades\Workflow;

class ResetPassword
{
    /**
     * @param $data
     * @throws \Exception
     */
    public function handle($data)
    {

        if(!Account::user()->resetPassword($data)){

            throw new \Exception(Lang::get('account.alerts.wrong_password_reset'));
        }

        Message::addSuccess(Lang::get('account.alerts.password_reset'));

        Workflow::redirect('account/login');
    }
}