<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Services\UrlGenerator;

class UrlServiceProvider extends ServiceProvider
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
        return [
            'url',
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['url'] = $this->app->share(function ($app){
            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $routes = $app['router']->getRoutes();

            return new UrlGenerator($routes, $app->rebinding('request', function ($app, $request){
                $app['url']->setRequest($request);
            }));
        });
    }

}