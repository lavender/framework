<?php

use Lavender\Entity\Facades\Attribute;
use Lavender\Entity\Facades\Relationship;
use Lavender\Store\Facades\Scope;

return [

    'category' => [
        'class' => 'Lavender\Catalog\Category',
        'scope' => Scope::IS_DEPARTMENT,
        'attributes' => [
            'name' => [
                'label' => 'Name',
                'type' => Attribute::VARCHAR,
            ],
            'description' => [
                'label' => 'Description',
                'type' => Attribute::TEXT,
            ],
            'url' => [
                'label' => 'Url',
                'type' => Attribute::VARCHAR,
                'before_save' => 'Lavender\Catalog\Handlers\CategoryUrl'
            ],
        ],
        'relationships' => [

            'products' => [
                'entity' => 'product',
                'type' => Relationship::HAS_PIVOT,
                'table' => 'catalog_category_product',
            ],
            'parent' => [
                'entity' => 'category',
                'type' => Relationship::BELONGS_TO,
            ],
            'children' => [
                'entity' => 'category',
                'type' => Relationship::HAS_MANY,
            ],

        ],

    ],



    'product' => [
        'class' => 'Lavender\Catalog\Product',
        'scope' => Scope::IS_STORE,
        'backend' => 'Lavender\Catalog\Backend\Product',
        'attributes' => [
            'sku' => [
                'label' => 'Sku',
                'type' => Attribute::VARCHAR,
            ],
            'name' => [
                'label' => 'Name',
                'type' => Attribute::VARCHAR,
            ],
            'price' => [
                'label' => 'Price',
                'type' => Attribute::DECIMAL,
                'default' => 0.00,
            ],
            'url' => [
                'label' => 'Url',
                'type' => Attribute::VARCHAR,
                'before_save' => 'Lavender\Catalog\Handlers\ProductUrl'
            ],
            'special_price' => [
                'label' => 'Price',
                'type' => Attribute::DECIMAL,
                'default' => 0.00,
            ],
        ],
        'relationships' => [

            'categories' => [
                'entity' => 'category',
                'type' => Relationship::HAS_PIVOT,
                'table' => 'catalog_category_product',
            ],

        ],
    ],


    'store' => [
        'relationships' => [
            'root_category' => [
                'entity' => 'category',
                'type' => Relationship::BELONGS_TO,
            ]
        ]
    ]


];
