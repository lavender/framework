<?php
namespace Lavender\Support\Traits;

trait RelationshipTrait
{

    public function applyRelationshipDefaults(array $values)
    {
        return recursive_merge([
            'entity' => null,
            'type'   => null,
            'table'  => null,
            'column' => null,
        ], $values);
    }


}