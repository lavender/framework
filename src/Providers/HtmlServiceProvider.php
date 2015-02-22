<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Services\HtmlBuilder;

class HtmlServiceProvider extends ServiceProvider
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
        return ['html'];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('html', function ($app){
            return new HtmlBuilder($app['url']);
        });
    }

}

