<?php
namespace Lavender\Support\Traits;

use Lavender\Database\Attribute;

trait AttributeTrait
{

    public function applyAttributeDefaults(array $values)
    {
        return recursive_merge([
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
            'handler'   => 'Lavender\Database\Attribute',
//            'before_save' => null,

            /**
             * Views
             */
            'label' => null,
//            'frontend.renderer' => null,
//            'backend.label' => null,
//            'backend.input' => 'text',
//            'backend.validate' => null,
//            'backend.renderer' => null,
//            'backend.table' => null,


            // todo support this stuff
            'length'   => null,
            'unsigned' => true,
            'comment'  => null,
        ], $values);
    }


}