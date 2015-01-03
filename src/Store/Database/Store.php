<?php
namespace Lavender\Store\Database;

use Lavender\Entity\Database\Entity;

class Store extends Entity
{

    protected $entity = 'store';

    protected $table = 'store';

    public $timestamps = false;

}