<?php
namespace Lavender\Database;

class Relationship
{
    /**
     * Entity Relationship Types
     */
    const HAS_PIVOT = 'pivot';
    const HAS_MANY = 'many';
    const HAS_ONE = 'hasone';
    const BELONGS_TO = 'belongs';

}