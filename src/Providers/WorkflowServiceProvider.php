<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;

class WorkflowServiceProvider extends ServiceProvider
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
            'workflow.factory',
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // factory is used to make valid workflow instances
        $this->registerFactory();
    }


    /**
     * Register the workflow builder
     */
    private function registerFactory()
    {
        $this->app->bind('workflow.factory', function ($app){

            return $app['Lavender\Services\WorkflowFactory'];
        });
    }

}