<?php
namespace Lavender\Html\Elements;

use Illuminate\Support\Facades\View;

class Table
{
    protected $layout = 'html::layouts.elements.table';

    protected $rows;

    protected $headers;

    /**
     * Add our table
     * @param $rows
     * @param array $headers
     * @param $layout
     * @return Table
     */
    public function make($rows, $headers, $layout)
    {
        $this->rows = $rows;

        $this->headers = $headers;

        if($layout) $this->layout = $layout;

        return $this;
    }

    /**
     * Render the table
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        try{

            return View::make($this->layout)
                ->with('id', uniqid('table-'))
                ->with('rows', $this->rows)
                ->with('headers', $this->headers)
                ->render();

        } catch(\Exception $e){

            //todo log exception
            var_dump($e->getMessage());

            return '';
        }
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