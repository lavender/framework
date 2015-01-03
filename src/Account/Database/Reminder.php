<?php
namespace Lavender\Account\Database;

use Lavender\Entity\Database\Entity;

class Reminder extends Entity
{

    protected $entity = 'reminder';

    protected $table = 'account_password_reminders';

    public $timestamps = false;

}