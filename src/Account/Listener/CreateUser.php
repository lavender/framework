<?php
namespace Lavender\Account\Listener;

class CreateUser
{

    public function handle($data)
    {
        if(!\Account::user()->findByEmail($data['email'])){

            \Account::user()->register($data);

            $user = \Account::user()->findByEmail($data['email']);

            if($user->id){

                if(\Config::get('store.signup_email')){

                    \Mail::queueOn(
                        'default',
                        \Config::get('store.email_account_confirmation'),
                        compact('user'),
                        function ($message) use ($user){
                            $message->to($user->email)->subject(\Lang::get('account.email.confirmation.subject'));
                        }
                    );

                    \Message::addSuccess(\Lang::get('account.alerts.instructions_sent'));
                } else{

                    \Message::addSuccess(\Lang::get('account.alerts.account_created'));
                }
            } else{

                throw new \Exception(implode(PHP_EOL, $user->errors()->all(':message')));
            }
        } else{

            throw new \Exception(\Lang::get('account.alerts.duplicated_credentials'));
        }
    }
}