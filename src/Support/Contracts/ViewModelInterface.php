<?php
namespace Lavender\Support\Contracts;

interface ViewModelInterface
{
    public function __construct(RendererInterface $renderer);

    public function render();

    public function with($key, $value);

    public function __toString();

}