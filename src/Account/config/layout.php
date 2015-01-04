<?php

return [

    'default' => [

        'account.login' => [

            'left' => [

                'register' => ['workflow' => 'register'],

            ],

            'right' => [

                'login' => ['workflow' => 'login'],

            ]

        ],

        'account.forgot_password' => [

            'content' => [

                'forgot_password' => ['workflow' => 'forgot_password']

            ]

        ],

        'account.reset_password' => [

            'content' => [

                'reset_password' => ['workflow' => 'reset_password']

            ]

        ],

    ]



];