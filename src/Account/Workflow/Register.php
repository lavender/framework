<?php
namespace Lavender\Account\Workflow;

use Lavender\Workflow\Contracts\WorkflowContract;

class Register implements WorkflowContract
{

    public function states($workflow)
    {
        return [

            10 => 'register_now',

            20 => 'create_user',

        ];
    }

    public function options($workflow, $state)
    {
        return [];
    }

    public function register_now()
    {
        return [

            'submit' => [
                'type' => 'button',
                'value' => 'Register now!',
                'options' => ['type' => 'submit'],
            ]

        ];
    }


    public function create_user()
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
                'flash' => false,
            ],

            'password_confirmation' => [
                'label' => 'Confirm Password',
                'type' => 'password',
                'validate' => ['required'],
                'flash' => false,
            ],

            'submit' => [
                'type' => 'button',
                'value' => 'Register',
                'options' => ['type' => 'submit'],
            ]

        ];
    }


}