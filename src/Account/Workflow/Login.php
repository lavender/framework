<?php
namespace Lavender\Account\Workflow;

use Lavender\Workflow\Contracts\WorkflowContract;

class Login implements WorkflowContract
{

    public function states($workflow)
    {
        return [

            10 => 'login_user',

        ];
    }

    public function options($workflow, $state)
    {
        return [];
    }

    public function login_user()
    {
        return [

            'email' => [
                'label' => 'Email',
                'type' => 'text',
                'validate' => ['required', 'email'],
            ],

            'password' => [
                'label' => 'Password',
                'type' => 'password',
                'validate' => ['required'],
                'comment' => "<a href='".URL::to('account/forgot_password')."'>Forgot your password?</a>",
                'flash' => false,
            ],

            'submit' => [
                'type' => 'button',
                'value' => 'Login',
                'options' => ['type' => 'submit'],
            ]

        ];
    }



}