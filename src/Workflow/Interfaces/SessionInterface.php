<?php
namespace Lavender\Workflow\Interfaces;

interface SessionInterface
{
    public function getId();

    public function setId($id);

    public function success($message = null);

    public function get();

    public function put($data);
}