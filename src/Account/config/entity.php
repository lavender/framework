<?php

use Lavender\Entity\Facades\Attribute;
use Lavender\Entity\Facades\Relationship;
use Lavender\Store\Facades\Scope;

return [

    'admin' => [
        'class' => 'Lavender\Account\Database\Admin',
        'scope' => Scope::IS_GLOBAL,
        'timestamps' => true,
        'attributes' => [
            'username' => [
                'label' => 'Username',
                'type' => Attribute::VARCHAR,
                'unique' => true,
                'backend.renderer' => 'Lavender\Backend\Handlers\Entity\EditLink',
            ],
            'email' => [
                'label' => 'Email',
                'type' => Attribute::VARCHAR,
                'unique' => true,
            ],
            'password' => [
                'label' => 'Password',
                'type' => Attribute::VARCHAR,
                'backend.table' => false,
            ],
            'remember_token' => [
                'label' => 'Remember Token',
                'type' => Attribute::VARCHAR,
                'nullable' => true,
                'backend.table' => false,
            ],
        ],
        'relationships' => [
            'workflows' => [
                'entity' => 'workflow.session',
                'type' => Relationship::HAS_MANY,
            ],
        ],
    ],

    'user' => [
        'class' => 'Lavender\Account\Database\User',
        'scope' => Scope::IS_STORE,
        'timestamps' => true,
        'attributes' => [
            'email' => [
                'label' => 'Email',
                'type' => Attribute::VARCHAR,
                'backend.renderer' => 'Lavender\Backend\Handlers\Entity\EditLink',
            ],
            'password' => [
                'label' => 'Password',
                'type' => Attribute::VARCHAR,
                'backend.table' => false,
            ],
            'confirmation_code' => [
                'label' => 'Confirmation Code',
                'type' => Attribute::VARCHAR,
                'backend.table' => false,
            ],
            'remember_token' => [
                'label' => 'Remember Token',
                'type' => Attribute::VARCHAR,
                'nullable' => true,
                'backend.table' => false,
            ],
            'confirmed' => [
                'label' => 'confirmed',
                'type' => Attribute::BOOL,
                'default' => false,
            ],
        ],
        'relationships' => [
            'workflows' => [
                'entity' => 'workflow.session',
                'type' => Relationship::HAS_MANY,
            ],
        ],
    ],

    'reminder' => [
        'class' => 'Lavender\Account\Database\Reminder',
        'scope' => Scope::IS_STORE,
        'timestamps' => true,
        'attributes' => [
            'email' => [
                'label' => 'Email',
                'type' => Attribute::VARCHAR,
            ],
            'token' => [
                'label' => 'Token',
                'type' => Attribute::VARCHAR,
            ],
            'created_at' => [
                'label' => 'Created At',
                'type' => Attribute::TIMESTAMP,
            ],
        ],
    ],






];
