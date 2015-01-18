<?php
namespace Lavender\Html;

use Illuminate\Support\ServiceProvider;

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
        $this->registerHtmlTable();

        $this->registerHtmlBuilder();
    }


    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    private function registerHtmlBuilder()
    {
        $this->app->bindShared('html', function ($app){
            return new Services\HtmlBuilder($app['url']);
        });
    }


    private function registerHtmlTable()
    {
        $this->app->bindShared('html.table', function ($app){

            return new Table\Builder;

        });

        $this->app->bindShared('html.table.entity', function ($app){

            return app('Lavender\Html\Table\Type\Entity');

        });

        $this->app->bindShared('html.table.config', function ($app){

            return app('Lavender\Html\Table\Type\Config');

        });

        $this->app->bindShared('html.table.basic', function ($app){

            return app('Lavender\Html\Table\Type\Basic');

        });
    }

}

