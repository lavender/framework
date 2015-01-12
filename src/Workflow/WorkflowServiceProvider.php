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

        // view model is returned by the factory and rendered
        $this->registerViewModel();
    }


    /**
     * Register the workflow factory
     */
    private function registerFactory()
    {
        $this->app->bindShared('workflow.factory', function ($app){

            $resolver = new Services\Resolver($app->config->get('workflow'));

            $repository = new Services\Repository($resolver);

            return new Services\Factory($repository, $app['events']);
        });
    }



    /**
     * Register the workflow view model
     */
    private function registerViewModel()
    {
        $this->app->bind('workflow.view', function($app, $params){

            list($workflow, $state, $config) = $params;

            $renderer = new Services\Renderer($app->view);

            $viewModel = new Services\ViewModel($workflow, $state, $config[$state]);

            $viewModel->setDefaultRenderer($renderer);

            return $viewModel;
        });
    }


}