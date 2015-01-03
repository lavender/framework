<?php
namespace Lavender\Workflow\Services;

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