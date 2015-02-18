<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Services\Workflow\Renderer;
use Lavender\Services\Workflow\Session;
use Lavender\Services\Workflow\Validator;
use Lavender\Services\WorkflowFactory;

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

            // session is used to save a user's current state for each workflow
            $session = new Session();

            // renderer is used to convert config to string html
            $renderer = new Renderer();

            // validator is used to validate user input
            $validator = new Validator();

            return new WorkflowFactory($session, $renderer, $validator);
        });
    }

}