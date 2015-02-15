<?php
namespace Lavender\Services\Workflow;

use Illuminate\Support\Facades\App;

class Factory
{
    /**
     * Get the evaluated view contents for the given workflow.
     *
     * @param  string $workflow
     * @param array $params
     * @return Builder
     */
    public function make($workflow, $params = [])
    {
        return App::make('workflow.builder', [$workflow, $params]);
    }

}