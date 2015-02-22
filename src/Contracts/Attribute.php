<?php
namespace Lavender\Contracts;

use Lavender\Support\Contracts\EntityInterface;

interface Attribute
{

    public function __construct(EntityInterface $entity, $key);

    public function __toString();

}