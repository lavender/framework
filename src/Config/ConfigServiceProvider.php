<?php
namespace Lavender\Config;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'lavender.config',
            'lavender.theme.config',
            'lavender.config.defaults'
        ];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/config', 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMergers();

        $this->app['lavender.config']->merge(['defaults']);

        $this->registerListeners();

        $this->app->booted(function (){

            $this->mergeConfig();
        });
    }

    private function registerMergers()
    {
        $this->app->bindShared('lavender.config', function ($app){

            return new Merger;
        });
        $this->app->bindShared('lavender.theme.config', function ($app){

            return new Merger;
        });
        $this->app->bindShared('lavender.config.defaults', function ($app){

            return new Merger;
        });
    }

    /**
     *  Register our listeners
     */
    private function registerListeners()
    {
        \Event::listen('lavender.theme', [$this, 'mergeThemeConfig']);
    }

    /**
     * Merge specific configs from all modules into global scope.
     */
    private function mergeConfig()
    {
        $namespaces = $this->app->config->getNamespaces();

        foreach($this->app['lavender.config']->getMerged() as $type){

            $globalConfig = $this->app->config[$type];

            foreach($namespaces as $namespace => $_ignored){

                $data = isset($this->app->config[$namespace . '::' . $type]) ?
                    $this->app->config[$namespace . '::' . $type] : [];

                foreach($data as $key => $values){

                    $global = isset($globalConfig[$key]) ?
                        $globalConfig[$key] : [];

                    $globalConfig[$key] = recursive_merge($global, $values);

                    $this->app->config->set($type, $globalConfig);
                }
            }
        }
    }

    public function mergeThemeConfig($theme)
    {
        // Cascade various configuration types based on current theme fallbacks.
        foreach($this->app['lavender.theme.config']->getMerged() as $type){

            $merged = [];

            $config = $this->app->config[$type];

            foreach($theme->fallbacks as $theme_code){

                if(isset($config[$theme_code])){

                    $merged = recursive_merge($config[$theme_code], $merged);
                }
            }

            $this->app->config[$type] = $merged;
        }

        // Now that theme config has been merged, merge predefined defaults.
        foreach($this->app['lavender.config.defaults']->getMerged() as $default){

            $config = $this->app->config[$default['config']];

            $type = $default['default'];

            $index = isset($default['index']) ? $default['index'] : null;

            $depth = isset($default['depth']) ? $default['depth'] : 1;

            array_walk_depth(
                $config,
                $depth,
                function (&$value) use ($type){
                    merge_defaults($value, $type);
                },
                $index
            );

            $this->app->config[$default['config']] = $config;
        }
    }
}