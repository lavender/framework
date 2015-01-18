<?php namespace Lavender\Html\Services;

use Illuminate\Html\HtmlBuilder as CoreHtmlBuilder;

class HtmlBuilder extends CoreHtmlBuilder
{
    function meta($attributes = [])
    {
        return '<meta' . $this->attributes($attributes) . ' />' . PHP_EOL;
    }

    public function table($table, $type = 'basic')
    {
        return app('html.table.'.$type)->with('table', $table);
    }
}
