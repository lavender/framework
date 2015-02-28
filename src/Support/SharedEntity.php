<?php
namespace Lavender\Support;

use Lavender\Contracts\Entity;

abstract class SharedEntity extends Shared
{

    function __construct($entity = null)
    {
        if($entity instanceof Entity){

            $this->setEntity($entity);

        }
    }

    public function setEntity(Entity $entity)
    {
        $this->unsetData();

        $this->addData(get_object_vars($entity));

        if($entity->exists){

            $this->addData($entity->getAttributes());

            $this->addData($entity->getRelationshipConfig(), function ($key, $val) use ($entity){

                return $entity->$key;
            });
        }
    }

}