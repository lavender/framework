<?php

return [

    'default' => [

        'account.login' => [

            'left' => [

                'register' => ['layout' => workflow('register')],

            ],

            'right' => [

                'login' => ['layout' => workflow('login')],

            ]

        ],

        'account.forgot_password' => [

            'content' => [

                'forgot_password' => ['layout' => workflow('forgot_password')]

            ]

        ],

        'account.reset_password' => [

            'content' => [

                'reset_password' => ['layout' => workflow('reset_password')]

            ]

        ],

    ]



];