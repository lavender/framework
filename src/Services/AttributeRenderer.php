<?php
namespace Lavender\Services;

class AttributeRenderer
{
    public function render($entity, $key)
    {
        return $entity->$key;
    }
}