<?php
namespace Lavender\Support;

use Lavender\Support\Contracts\EntityInterface;

abstract class SharedEntity extends Shared
{

    public function setEntity(EntityInterface $entity)
    {
        $this->unsetData();

        $this->addData(get_object_vars($entity));

        $this->addData($entity->getAttributes());

        $this->addData($entity->getRelationships(), function($key) use ($entity){
            return $entity->$key;
        });
    }

}