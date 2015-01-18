<?php
namespace Lavender\Html\Table\Type;

use Lavender\Entity\Database\Repository;

class Entity extends Basic
{
    public $layout = 'layouts.elements.table.entity';

    public $attributes = [];

    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function render()
    {
        $this->columns = $this->repository->columns();

        $this->rows = $this->repository->rows();

        $this->table = $this->repository->entity->getEntity();

        return parent::render();
    }

    public function with($key, $value)
    {
        $this->repository->with($key, $value);

        return $this;
    }

}