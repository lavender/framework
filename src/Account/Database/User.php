<?php
namespace Lavender\Account\Database;

class User extends Account
{

    protected $entity = 'user';

    protected $table = 'account_user';

    public $rules = [
        'create' => [
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ],
        'update' => [
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ]
    ];

}