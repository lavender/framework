<?php
namespace Lavender\Account\Database;

class Admin extends Account
{

    protected $entity = 'admin';

    protected $table = 'account_admin';

    public $confirmed = true;

    public $rules = [
        'create' => [
            'username' => 'required|min:4',
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ],
        'update' => [
            'username' => 'required|min:4',
            'email'    => 'required|email',
            'password' => 'required|min:4',
        ]
    ];

}