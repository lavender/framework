<?php
namespace Lavender\View\Html\Table;

use Lavender\Entity\Database\Repository;

class Database extends Basic
{
    public $layout = 'layouts.elements.table.database';

    public $attributes = [];

    protected $repository = null;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function render()
    {
        $this->columns = $this->repository->columns();

        $this->rows = $this->repository->rows();

        return parent::render();
    }

    public function with($key, $value)
    {
        $this->repository->with($key, $value);

        return $this;
    }

}