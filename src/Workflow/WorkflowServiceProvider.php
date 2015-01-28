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
            'workflow.factory'
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
    }


    /**
     * Register the workflow factory
     */
    private function registerFactory()
    {
        $this->app->bindShared('workflow.factory', function ($app){

            $session = new Services\Session();

            $config = new Services\Config($app->config->get('workflow'));

            $validator = new Services\Validator();

            $renderer = new Services\Renderer($app->view);

            return new Services\Factory($session, $config, $validator, $renderer);
        });
    }


}