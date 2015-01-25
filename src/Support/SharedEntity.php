<?php
namespace Lavender\Support;

use Lavender\Support\Contracts\EntityInterface;

abstract class SharedEntity extends Shared
{

    function __construct($entity = null)
    {
        if($entity instanceof EntityInterface){

            $this->setEntity($entity);

        }
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->unsetData();

        $this->addData(get_object_vars($entity));

        if($entity->exists){

            $this->addData($entity->getAttributes());

            $this->addData($entity->getRelationships(), function ($key, $val) use ($entity){

                return $entity->$key;
            });
        }
    }

}