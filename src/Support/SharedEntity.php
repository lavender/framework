<?php
namespace Lavender\Support;

use Lavender\Support\Contracts\EntityInterface;

abstract class SharedEntity extends Shared
{

    public function setEntity(EntityInterface $entity)
    {
        $this->unsetData();

        $relationships = $entity->getRelationships();

        $attributes = $entity->getAttributes();

        foreach($relationships as $r) $this->$r = $entity->$r;

        foreach($attributes as $a => $attr) $this->$a = $attr;
    }

}