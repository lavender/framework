<?php
namespace Lavender\Html\Table\Type;

use Lavender\Entity\Database\Repository;
use Lavender\Support\Facades\Message;

class Entity extends Basic
{
    public $layout = 'layouts.elements.table.entity';

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
        try{

            $this->columns = $this->repository->columns();

            $this->rows = $this->repository->rows();

            $this->table = $this->repository->entity->getEntity();

            return parent::render();

        }catch (\Exception $e){

            //todo log error
            Message::addError($e->getMessage());

            return '';

        }
    }

    public function with($key, $value)
    {
        $this->repository->with($key, $value);

        return $this;
    }

}