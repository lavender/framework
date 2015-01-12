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
                'backend.renderer' => 'Lavender\Backend\Handlers\Entity\EditLink',
            ],
            'name' => [
                'label' => 'Name',
                'type' => Attribute::VARCHAR,
                'backend.renderer' => 'Lavender\Backend\Handlers\Entity\EditLink',
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