<?php
namespace Lavender\View\Html\Table;

class Basic
{

    public $layout = 'layouts.elements.table.basic';

    public $columns = [];

    public $rows = [];

    /**
     * Render the table
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        if(!isset($this->table)) throw new \Exception('Invalid instantiation of '.get_class($this));

        \Event::fire('html.table.'.$this->table, [$this]);

        return \View::make($this->layout)
            ->with('columns', $this->columns)
            ->with('rows', $this->rows)
            ->render();
    }

    /**
     * Add a piece of data to the table.
     *
     * @param  string|array $key
     * @param  mixed $value
     * @return $this
     */
    public function with($key, $value)
    {
        $this->$key = $value;

        return $this;
    }


    /**
     * Dynamically bind parameters to the table.
     *
     * @param  string $method
     * @param  array $parameters
     * @return $this
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if(starts_with($method, 'with')){
            return $this->with(snake_case(substr($method, 4)), $parameters[0]);
        }

        throw new \BadMethodCallException("Method [$method] does not exist on table.");
    }


    /**
     * Get the string contents of the table.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}