<?php
namespace Lavender\Support\Contracts;

interface WorkflowInterface
{
    public function __construct(RendererInterface $renderer);

    public function render();

    public function next($state, $request);

    public function with($key, $value);

    public function __toString();

}