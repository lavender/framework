<?php
namespace Lavender\Html\Table;

class Builder
{
    public function make($table, $type = 'basic')
    {
        return app('html.table.'.$type)->with('table', $table);
    }
}