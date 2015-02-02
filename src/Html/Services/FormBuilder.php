<?php namespace Lavender\Html\Services;

use Illuminate\Html\FormBuilder as CoreFormBuilder;
use Illuminate\Support\Facades\HTML;

class FormBuilder extends CoreFormBuilder
{

    public function table($rows, $headers = [], $layout = 'html::layouts.elements.form.table')
    {
        // todo define selected in layout

        return HTML::table($rows, $headers, $layout);
    }
}
