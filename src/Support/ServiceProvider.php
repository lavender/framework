<?php
namespace Lavender\Support;

use Illuminate\Support\ServiceProvider as CoreServiceProvider;

abstract class ServiceProvider extends CoreServiceProvider
{

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, recursive_merge(require $path, $config));
    }

}