<?php
namespace Lavender\Workflow\Exceptions;

class StateException extends \Exception
{

    protected $errors;

    public function __construct($message = "", $errors = NULL)
    {
        $this->errors = $errors;

        parent::__construct($message);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}