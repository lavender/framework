<?php
namespace Lavender\Workflow\Interfaces;

interface RepositoryInterface
{
    public function __construct(SessionInterface $session);

    public function findOrNew();

    public function first();

    public function next();

    public function setState($state);

    public function findBySession();

    public function model(WorkflowInterface $model);
}