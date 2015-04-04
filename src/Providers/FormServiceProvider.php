<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
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
            'form.factory',
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // factory is used to make valid form instances
        $this->registerFactory();
    }


    /**
     * Register the form builder
     */
    private function registerFactory()
    {
        $this->app->bind('form.factory', function ($app){

            return $app['Lavender\Services\FormFactory'];
        });
    }

}