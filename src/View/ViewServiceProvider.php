<?php
namespace Lavender\View;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
            'asset.publisher',
            'layout.injector',
            'page.router',
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAssetPublisher();

        $this->registerPageRouter();

        $this->registerUrlGenerator();

        $this->registerLayoutInjector();
    }


    private function registerPageRouter()
    {
        $this->app->bindShared('page.router', function ($app){

            return new Services\PageRouter();

        });
    }

    private function registerAssetPublisher()
    {
        $this->app->bindShared('asset.publisher', function ($app){

            // Overriding the default publisher to allow publishing
            // directly into public directory.
            $publisher = new Services\AssetPublisher($app['files'], $app['path.public']);

            $publisher->setPackagePath($app['path.base'] . '/vendor');

            return $publisher;
        });
    }


    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(function ($app){
            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $routes = $app['router']->getRoutes();

            return new Services\UrlGenerator($routes, $app->rebinding('request', function ($app, $request){
                $app['url']->setRequest($request);
            }));
        });
    }


    /**
     * Register layout injection service
     */
    private function registerLayoutInjector()
    {
        $this->app->bindShared('layout.injector', function (){
            return new Services\LayoutInjector;
        });
    }

}

