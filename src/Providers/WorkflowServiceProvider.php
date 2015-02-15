<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Services\Workflow\Builder;
use Lavender\Services\Workflow\Factory;
use Lavender\Services\Workflow\Renderer;
use Lavender\Services\Workflow\Session;
use Lavender\Services\Workflow\Validator;

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
        $this->app->singleton('workflow.factory', function ($app){

            return new Factory;
        });
    }


    /**
     * Register the workflow builder
     */
    private function registerBuilder()
    {
        $this->app->bind('workflow.builder', function ($app, $user_params){

            list($workflow, $params) = $user_params;

            $config = $app->config->get('workflow', []);

            $classes = isset($config[$workflow]) ? $config[$workflow] : [];

            ksort($classes);

            return new Builder($workflow, $classes, $params);
        });
    }


    /**
     * Register the workflow renderer
     */
    private function registerRenderer()
    {
        $this->app->bind('workflow.renderer', function ($app, $user_params){

            list($template, $options, $fields, $identity) = $user_params;

            return new Renderer($template, $options, $fields, $identity);
        });
    }


    /**
     * Register the workflow session
     */
    private function registerSession()
    {
        $this->app->singleton('workflow.session', function ($app){

            return new Session;
        });
    }


    /**
     * Register the workflow validator
     */
    private function registerValidator()
    {
        $this->app->singleton('workflow.validator', function ($app){

            return new Validator;
        });
    }


}