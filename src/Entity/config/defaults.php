<?php

use Lavender\Entity\Facades\Attribute;

return [

    'entity'            => [
        'class'         => null,
        'backend'       => null,
        'attributes'    => [],
        'relationships' => [],
    ],

    'attribute'         => [
        'label'    => null,
        'type'     => Attribute::VARCHAR,
        'parent'   => false,
        'default'  => null,
        'nullable' => true,
        'unique'   => false,
        'before_save'   => false,

        // todo support this stuff
        'length'   => null,
        'unsigned' => true,
        'comment'  => null,
    ],

    'relationship' => [
        'entity' => null,
        'type' => null,
        'table' => null,
        'column' => null,
    ],


];
