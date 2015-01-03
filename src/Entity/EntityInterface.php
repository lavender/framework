<?php
namespace Lavender\Entity;

interface EntityInterface
{
    // Attribute Data Types
    const ATTRIBUTE_DATE = 'date';
    const ATTRIBUTE_DECIMAL = 'decimal';
    const ATTRIBUTE_INTEGER = 'int';
    const ATTRIBUTE_TEXT = 'text';
    const ATTRIBUTE_VARCHAR = 'varchar';

    // Entity Relationship Types
    const HAS_PIVOT = 'pivot';
    const HAS_MANY = 'many';
    const HAS_ONE = 'one';
    const BELONGS_TO = 'belongs';
}