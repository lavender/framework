<?php
namespace Lavender\Entity\Database;

class Attribute
{
    // Attribute Data Types
    const DATE = 'date';
    const DECIMAL = 'decimal';
    const INTEGER = 'int';
    const TEXT = 'text';
    const VARCHAR = 'varchar';

    // Entity Relationship Types
    const HAS_PIVOT = 'pivot';
    const HAS_MANY = 'many';
    const HAS_ONE = 'one';
    const BELONGS_TO = 'belongs';
}