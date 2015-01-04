<?php
namespace Lavender\Catalog\Database;

use Lavender\Entity\Database\Entity;

class Category extends Entity
{

    protected $entity = 'category';

    protected $table = 'catalog_category';

    public $timestamps = true;

}