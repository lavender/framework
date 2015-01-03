<?php

return [

    'admin' => [
        'class' => 'Lavender\Account\Admin',
        'scope' => Lavender::SCOPE_GLOBAL,
        'timestamps' => true,
        'attributes' => [
            'email' => [
                'label' => 'Email',
                'type' => 'varchar',
                'unique' => true,
            ],
            'username' => [
                'label' => 'Username',
                'type' => 'varchar',
                'unique' => true,
            ],
            'password' => [
                'label' => 'Password',
                'type' => 'varchar',
            ],
            'remember_token' => [
                'label' => 'Remember Token',
                'type' => 'varchar',
                'nullable' => true,
            ],
        ],
        'relationships' => [
            'workflows' => [
                'entity' => 'workflow.session',
                'type' => Lavender::HAS_MANY,
            ],
        ],
    ],

    'user' => [
        'class' => 'Lavender\Account\User',
        'scope' => Lavender::SCOPE_STORE,
        'timestamps' => true,
        'attributes' => [
            'email' => [
                'label' => 'Email',
                'type' => 'varchar',
            ],
            'password' => [
                'label' => 'Password',
                'type' => 'varchar',
            ],
            'confirmation_code' => [
                'label' => 'Confirmation Code',
                'type' => 'varchar',
            ],
            'remember_token' => [
                'label' => 'Remember Token',
                'type' => 'varchar',
                'nullable' => true,
            ],
            'confirmed' => [
                'label' => 'confirmed',
                'type' => 'bool',
                'default' => false,
            ],
        ],
        'relationships' => [
            'workflows' => [
                'entity' => 'workflow.session',
                'type' => Lavender::HAS_MANY,
            ],
        ],
    ],

    'reminder' => [
        'class' => 'Lavender\Account\Reminder',
        'scope' => Lavender::SCOPE_STORE,
        'timestamps' => true,
        'attributes' => [
            'email' => [
                'label' => 'Email',
                'type' => 'varchar',
            ],
            'token' => [
                'label' => 'Token',
                'type' => 'varchar',
            ],
            'created_at' => [
                'label' => 'Created At',
                'type' => 'timestamp',
            ],
        ],
    ],






];
