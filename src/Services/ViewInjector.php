<?php
namespace Lavender\Services;

class ViewInjector
{
    /**
     * Inject views into layouts
     *
     * @param $section
     * @param array $config
     */
    public function append($section, array $config)
    {
        $config = array_merge([
            'content' => null,
            'config' => null,
            'layout' => null,
            'script' => null,
            'menu' => null,
            'style' => null,
            'workflow' => null,
            'meta' => null,
            'position' => 0
        ], $config);

        if($html = $this->renderByType($config)){

            view()->inject($section, '@parent' . PHP_EOL . $html);

        }
    }

    /**
     * Render the layout html by type (see View/config/defaults.php)
     * @param $config
     * @return bool
     */
    protected function renderByType($config)
    {
        if($config['script']){

            return \HTML::script($config['script']);

        } elseif($config['meta']){

            return \HTML::meta($config['meta']);

        } elseif($config['style']){

            return \HTML::style($config['style']);

        } elseif($config['menu']){

            return menu($config['menu']);

        } elseif(workflow()->exists($config['workflow'])){

            return workflow($config['workflow']);

        } elseif(view()->exists($config['layout'])){

            //todo do not pre-render
            return view($config['layout'])->render();

        } elseif($config['config']){

            return config($config['config']);

        }

        return false;
    }
}