<?php
namespace Lavender\Workflow\Interfaces;

interface ModelInterface
{
    public function __construct(RepositoryInterface $repo, RendererInterface $renderer);

    public function register($workflow, array $config);

    public function handle($state);

    public function render();

    public function firstSession();

    public function nextSession();

    public function findSession();

    public function getStates($include_config = false);

    public function defaultState();

    public function nextState();

    public function hasState($state);

    public function getWorkflow();

    public function getState();
}