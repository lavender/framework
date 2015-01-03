<?php

use Lavender\Entity\Facades\Attribute;
use Lavender\Entity\Facades\Relationship;

return [


    /**
     * Theme model
     * Used to describe themes, locales.
     */
    'theme' => [
        'class' =>  'Lavender\View\Database\Theme',
        'attributes' => [
            'code' => [
                'label' => 'Code',
                'type' => Attribute::VARCHAR,
                'unique' => true,
            ],
            'name' => [
                'label' => 'Name',
                'type' => Attribute::VARCHAR,
            ],
        ],
        'relationships' => [
            'parent' => [
                'entity' => 'theme',
                'type' => Relationship::HAS_ONE,
            ],
        ],
    ],


];