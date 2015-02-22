<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Services\ViewInjector;

class LayoutServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['view.injector'];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('view.injector', function(){

            return new ViewInjector();

        });
    }

}