<?php
namespace Lavender\View\Html;

class Table
{
    public function make($table, $type = 'basic')
    {
        return app('html.table.'.$type)->with('table', $table);
    }
}