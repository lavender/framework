<?php
namespace Lavender\Services;

use Lavender\Support\Facades\Layout;
use Lavender\Support\Workflow;

class LayoutInjector
{
    /**
     * Inject views into layouts
     *
     * @param array $sections
     */
    public function inject(array $sections)
    {
        foreach($sections as $sectionName => $children){

            // Sort $children by 'position'
            sort_children($children);

            // Inject $children backwards so they render in the correct order
            $children = array_reverse($children);

            foreach($children as $childName => $childConfig){

                //todo prevent render by $childName

                $childConfig = array_merge([
                    'content' => null,
                    'config' => null,
                    'layout' => null,
                    'script' => null,
                    'style' => null,
                    'workflow' => null,
                    'meta' => null,
                    'position' => 0,
                    'mode'  => Layout::APPEND
                ], $childConfig);

                if($html = $this->renderByType($childConfig)){

                    \View::startSection($sectionName, '@parent' . PHP_EOL . $html);

                }

            }

        }
    }

    /**
     * Render the layout html by type (see View/config/defaults.php)
     * @param $config
     * @return bool
     */
    private function renderByType($config)
    {
        if($config['script']){

            return \HTML::script($config['script']);

        } elseif($config['meta']){

            return \HTML::meta($config['meta']);

        } elseif($config['style']){

            return \HTML::style($config['style']);

        } elseif($config['workflow']){

            return Workflow::make($config['workflow']);

        } elseif(view()->exists($config['layout'])){

            //todo do not pre-render
            return view($config['layout']);

        } elseif($config['config']){

            return config($config['config']);

        }

        return false;
    }
}