<?php
namespace Lavender\View\Database;

use Lavender\Entity\Database\Entity;

class Theme extends Entity
{

    protected $entity = 'theme';

    protected $table = 'theme';

    public $timestamps = false;

}