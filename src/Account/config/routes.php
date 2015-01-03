<?php

return [

    'default' => [

        // login page
        'account/login' => [
            'layout' => 'account.login'
        ],

        // forgot password page
        'account/forgot_password' => [
            'layout' => 'account.forgot_password',
        ],

        // logout action
        'account/logout' => [
            'controller' => 'Lavender\Account\Routing\UserController',
            'action'     => 'logout',
        ],

        // reset password
        'account/reset_password/{token}' => [
            'controller' => 'Lavender\Account\Routing\UserController',
            'action'     => 'resetPassword',
        ],

        // hide token in field and show reset password field
        'account/reset_password' => [
            'controller' => 'Lavender\Account\Routing\UserController',
            'action'     => 'doReset',
        ],

        // confirm registration
        'account/confirm/{code}' => [
            'controller' => 'Lavender\Account\Routing\UserController',
            'action'     => 'confirm',
        ],
    ],

];