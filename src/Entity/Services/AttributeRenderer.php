<?php
namespace Lavender\Entity\Services;

class AttributeRenderer
{
    public function render($entity, $key)
    {
        return $entity->$key;
    }
}