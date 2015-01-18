<?php
namespace Lavender\Workflow;

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
            'workflow.view'
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // factory is used to make workflow instances
        $this->registerFactory();

        // view model is built by the factory and rendered
        $this->registerViewModel();
    }


    /**
     * Register the workflow factory
     */
    private function registerFactory()
    {
        $this->app->bindShared('workflow.factory', function ($app){

            $session = new Services\Session();

            $resolver = new Services\Resolver($app->config->get('workflow'));

            $validator = new Services\Validator();

            return new Services\Factory($session, $resolver, $validator);
        });
    }



    /**
     * Register the workflow view model
     */
    private function registerViewModel()
    {
        $this->app->bind('workflow.view', function($app){

            $renderer = new Services\Renderer($app->view);

            return new Services\Workflow($renderer);
        });
    }


}