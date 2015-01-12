<?php
namespace Lavender\Workflow\Contracts;

use Illuminate\View\Factory;

interface RendererInterface
{
    public function __construct(Factory $factory);

    public function render(ViewModelInterface $view);
}