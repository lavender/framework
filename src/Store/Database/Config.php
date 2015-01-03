<?php
namespace Lavender\Store\Database;

use Lavender\Entity\Database\Entity;

class Config extends Entity
{

    protected $entity = 'store.config';

    protected $table = 'store_config';

    public $timestamps = false;

}