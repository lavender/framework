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
            'html.table',
            'asset.publisher',
            'layout.injector',
            'menu.builder',
            'message.service',
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerHtmlTable();

        $this->registerMessageBag();

        $this->registerMenuBuilder();

        $this->registerAssetPublisher();

        $this->registerUrlGenerator();

        $this->registerLayoutInjector();
    }


    private function registerHtmlTable()
    {
        $this->app->bindShared('html.table', function ($app){

            return new Html\Table;

        });

        $this->app->bindShared('html.table.database', function ($app){

            return app('Lavender\View\Html\Table\Database');

        });

        $this->app->bindShared('html.table.config', function ($app){

            return app('Lavender\View\Html\Table\Config');

        });

        $this->app->bindShared('html.table.basic', function ($app){

            return app('Lavender\View\Html\Table\Basic');

        });
    }


    private function registerMessageBag()
    {
        $this->app->bindShared('message.service', function ($app){

            return new Services\MessageBag;

        });
    }


    private function registerMenuBuilder()
    {
        $this->app->bindShared('menu.builder', function ($app){

            return new Services\MenuBuilder;

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

