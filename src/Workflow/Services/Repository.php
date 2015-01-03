<?php
namespace Lavender\Workflow\Services;

use Lavender\Workflow\Interfaces\ModelInterface;
use Lavender\Workflow\Interfaces\SessionInterface;
use Lavender\Workflow\Interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
{

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var SessionInterface
     */
    protected $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Find workflow or create new
     * @return mixed
     */
    public function findOrNew()
    {
        if($found = $this->findBySession()) return $found;

        return $this->first();
    }

    /**
     * Set default state
     */
    public function first()
    {
        return $this->setState($this->model->defaultState());
    }

    /**
     * Set next state
     */
    public function next()
    {
        return $this->setState($this->model->nextState());
    }

    /**
     * @param $state
     * @return mixed
     */
    public function setState($state)
    {
        $values = ['state' => $state];

        $this->_put($values);

        return $this->findBySession();
    }

    /**
     * Find by session
     * @return mixed
     */
    public function findBySession()
    {
        if($found = $this->_get()){

            $found = (object)$found;

            if($this->model->hasState($found->state)) return $found;

        }

        return false;
    }

    /**
     * Get and set the current model
     *
     * @param Model $model
     * @return void
     */
    public function model(ModelInterface $model)
    {
        $this->model = $model;
    }

    protected function _get()
    {
        return $this->session->get();
    }

    protected function _put($data)
    {
        return $this->session->put($data);
    }

}