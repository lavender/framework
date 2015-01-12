<?php
namespace Lavender\Workflow\Contracts;

interface ViewModelInterface
{
    public function __construct($workflow, $state, $config);

    public function render();

    public function with($key, $value);

    public function getWorkflow();

    public function getState();

    public function __toString();


//    public function handle($state);
//
//    public function render();
//
//    public function firstSession();
//
//    public function nextSession();
//
//    public function findSession();
//

}