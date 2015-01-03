<?php
namespace Lavender\Workflow\Interfaces;

use Illuminate\View\Factory;

interface RendererInterface
{
    public function __construct(Factory $factory);

    public function make($workflow, $state, $config);

    public function render();
}