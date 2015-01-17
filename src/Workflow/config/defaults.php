<?php


return [
//
//    'workflow-state' => [
//        'fields' => [],
//        'before' => [],
//        'after' => [],
//        'layout' => null,
//        'renderer' => null,
//        'redirect' => null,
//    ],

    'workflow-field' => [
        // field label (optional)
        'label' => null,
        'label_options' => [],

        // applies to all fields
        'type' => 'text',
        'position' => 0,
        'name' => null,
        'value' => null,
        'options' => ['id' => null],
        'validate' => [],
        'comment' => null,
        'flash' => true,

        //applies to select fields
        'values' => [],

        //applies to checkbox & radio fields
        'checked' => [],
    ],


];
