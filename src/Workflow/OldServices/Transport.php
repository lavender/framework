<?php
namespace Lavender\Workflow\OldServices;

/**
 * Class Transport is used to transport the workflow form data
 * to 'before' callbacks prior to being rendered.
 *
 * @package Lavender\Workflow\Services
 */
class Transport
{

    /**
     * Options passed to Form:open()
     * @var array
     */
    public $options = [];

    /**
     * Prepared workflow fields
     * @var array
     */
    public $fields = [];

    /**
     * Prepare workflow layout
     * @var null
     */
    public $layout = null;

}