<?php
namespace Lavender\Services;

use Illuminate\Html\HtmlBuilder as CoreHtmlBuilder;

class HtmlBuilder extends CoreHtmlBuilder
{
    function meta($attributes = [])
    {
        return '<meta' . $this->attributes($attributes) . ' />' . PHP_EOL;
    }
}
