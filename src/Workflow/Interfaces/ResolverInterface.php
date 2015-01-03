<?php
namespace Lavender\Workflow\Interfaces;

interface ResolverInterface
{
    public function register($workflow, ModelInterface $model);

    public function resolve($workflow);
}