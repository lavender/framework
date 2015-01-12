<?php
namespace Lavender\View\Database;

use Lavender\Entity\Database\Entity;
use Lavender\Entity\Traits\BootableEntity;

class Theme extends Entity
{
    use BootableEntity;

    protected $entity = 'theme';

    protected $table = 'theme';

    public $timestamps = false;
}