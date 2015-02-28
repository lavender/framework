<?php
namespace Lavender\Database;

use Lavender\Contracts\Entity as EntityContract;
use Lavender\Contracts\Entity\Attribute as AttributeContract;

class Attribute implements AttributeContract
{
    /**
     * Entity Attribute Types
     */
    const DATE      = 'date';
    const DECIMAL   = 'decimal';
    const INDEX     = 'index';
    const INTEGER   = 'int';
    const BOOL      = 'bool';
    const TEXT      = 'text';
    const TIMESTAMP = 'timestamp';
    const VARCHAR   = 'varchar';
    
    protected $entity;

    protected $key;

    public function __construct(EntityContract $entity, $key)
    {
        $this->entity = $entity;

        $this->key = $key;
    }

    public function value()
    {
        return $this->entity->{$this->key};
    }

    public function backend()
    {
        return $this->value();
    }

    public function before_save($value)
    {
        return $value;
    }

    public function __toString()
    {
        return (string)$this->value();
    }
}