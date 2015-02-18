<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Services\FormBuilder;
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
        return ['html', 'form'];
    }


    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
//        $this->registerHtmlTable();

        $this->registerFormBuilder();

        $this->registerHtmlBuilder();
    }


    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    private function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app){
            return new HtmlBuilder($app['url']);
        });
    }


    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app){
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }


//    private function registerHtmlTable()
//    {
//        $this->app->bind('html.elements.table', function ($app){
//
//            return new Elements\Table();
//
//        });
//    }

}

