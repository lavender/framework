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

        // builder is used to generate config from workflow instance
        $this->registerBuilder();

        // renderer is used to convert config to string html
        $this->registerRenderer();

        // session is used to save a user's current state for each workflow
        $this->registerSession();

        // validator is used to validate user input
        $this->registerValidator();
    }


    /**
     * Register the workflow builder
     */
    private function registerFactory()
    {
        $this->app->bindShared('workflow.factory', function ($app){

            $session = new Services\Session();

            $validator = new Services\Validator();

            return new Services\Factory($session, $validator);
        });
    }


    /**
     * Register the workflow builder
     */
    private function registerBuilder()
    {
        $this->app->bind('workflow.builder', function ($app, $user_params){

            list($workflow, $params) = $user_params;

            $classes = $app->config->get('workflow.'.$workflow, []);

            ksort($classes);

            return new Services\Builder($workflow, $classes, $params);
        });
    }


    /**
     * Register the workflow renderer
     */
    private function registerRenderer()
    {
        $this->app->bind('workflow.renderer', function ($app, $user_params){

            list($template, $options, $fields, $identity) = $user_params;

            return new Services\Renderer($template, $options, $fields, $identity);
        });
    }


    /**
     * Register the workflow session
     */
    private function registerSession()
    {
        $this->app->bindShared('workflow.session', function ($app){

            return new Services\Session();
        });
    }


    /**
     * Register the workflow validator
     */
    private function registerValidator()
    {
        $this->app->bindShared('workflow.validator', function ($app){

            return new Services\Validator();
        });
    }


}