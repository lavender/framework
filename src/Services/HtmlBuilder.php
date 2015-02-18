<?php
namespace Lavender\Services;

use Illuminate\Html\HtmlBuilder as CoreHtmlBuilder;

class HtmlBuilder extends CoreHtmlBuilder
{
    function meta($attributes = [])
    {
        return '<meta' . $this->attributes($attributes) . ' />' . PHP_EOL;
    }

//    public function table($rows, $headers = [], $layout = null)
//    {
//        return app('html.elements.table')->make($rows, $headers, $layout);
//    }
}
