<?php

use Lavender\Support\Facades\Attribute;

return [

    'entity'            => [
        'class'         => null,
        'attributes'    => [],
        'relationships' => [],
    ],

    'attribute'         => [
        /**
         * Database
         */
        'type'     => Attribute::VARCHAR,
        'default'  => null,
        'nullable' => true,
        'unique'   => false,

        /**
         * Entity
         */
        'parent'   => false,
        'before_save' => null,

        /**
         * Views
         */
        'label' => null,
        'frontend.renderer' => null,
        'backend.label' => null,
        'backend.input' => 'text',
        'backend.validate' => null,
        'backend.renderer' => null,
        'backend.table' => null,


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
