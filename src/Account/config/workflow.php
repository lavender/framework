<?php

return [

    'default' => [

        'register' => [

            'call_to_action' => [

                'fields' => [

                    'submit' => [
                        'type' => 'button',
                        'value' => 'Register now!',
                        'options' => ['type' => 'submit'],
                    ]

                ]

            ],

            'create_user' => [

                'after' => [

                    'create-user' => ['class' => 'Lavender\Account\Handlers\CreateUser']

                ],

                'fields' => [

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

                ]

            ],

        ],

        'login' => [

            'login_user' => [

                'after' => [

                    'create-user' => ['class' => 'Lavender\Account\Handlers\DoLogin']

                ],

                'fields' => [

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

                ],

            ],

        ],

        'forgot_password' => [

            'request_reset' => [

                'redirect' => 'account/login',

                'after' => [

                    'create-user' => ['class' => 'Lavender\Account\Handlers\ForgotPassword']

                ],

                'fields' => [

                    'email' => [
                        'label' => 'Email',
                        'type' => 'text',
                        'validate' => ['required', 'email'],
                    ],

                    'submit' => [
                        'type' => 'button',
                        'value' => 'Reset',
                        'options' => ['type' => 'submit'],
                    ]

                ],

            ],

        ],

        'reset_password' => [

            'do_reset' => [

                'redirect' => 'account/login',

                'after' => [

                    'reset' => ['class' => 'Lavender\Account\Handlers\ResetPassword']

                ],

                'fields' => [

                    'token' => [
                        'type' => 'hidden',
                        'validate' => ['required'],
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
                        'value' => 'Reset',
                        'options' => ['type' => 'submit'],
                    ]

                ],

            ],

        ],

    ],

];
