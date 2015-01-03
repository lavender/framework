<?php
namespace Lavender\Workflow;

use Illuminate\Database\QueryException;
use Illuminate\Support\ServiceProvider;
use Lavender\Workflow\Exceptions\StateException;

class WorkflowServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'workflow',
            'workflow.session',
            'workflow.repository',
            'workflow.renderer'
        ];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/workflow', 'workflow', realpath(__DIR__));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        $this->registerSession();

        $this->registerRepository();

        $this->registerRenderer();

        $this->registerWorkflow();

        $this->app->booted(function (){

            // once booted, register the resolver for the consumer
            $this->registerResolver();

            // once booted, register the routes for the consumer
            $this->registerRoutes();
        });
    }

    /**
     * Merge workflow config
     */
    private function registerConfig()
    {
        $this->app['lavender.config']->merge(['workflow']);

        $this->app['lavender.theme.config']->merge(['workflow']);

        $merge_workflow_state = [
            'config' => 'workflow',
            'default' => 'workflow-state',
            'depth' => 2,
        ];

        $merge_workflow_fields = [
            'config' => 'workflow',
            'default' => 'workflow-field',
            'index' => 'fields',
            'depth' => 4,
        ];

        $this->app['lavender.config.defaults']->merge([$merge_workflow_state, $merge_workflow_fields]);
    }


    /**
     * Register the workflow session
     */
    private function registerSession()
    {
        $this->app->bindShared('workflow.session', function ($app){

            return new Services\Session;
        });
    }

    /**
     * Register the workflow repository
     */
    private function registerRepository()
    {
        $this->app->bind('workflow.repository', function ($app){

            return new Services\Repository($app['workflow.session']);
        });
    }

    /**
     * Register the state renderer
     */
    protected function registerRenderer()
    {
        $this->app->bindShared('workflow.renderer', function ($app){

            return new Services\Renderer($app->view);
        });
    }

    /**
     * Register the workflow model
     */
    private function registerWorkflow()
    {
        $this->app->bind('workflow', function ($app){

            return new Services\Workflow($app['workflow.repository'], $app['workflow.renderer']);
        });
    }

    /**
     * Register the workflow post requests
     */
    private function registerRoutes()
    {
        $route = $this->app->config['store.workflow_base_url'] . '/{workflow}/{state}';

        \Route::post($route, function ($workflow, $state){

            $errors = null;

            $redirect = null;

            try{

                /** @var Model $model */
                $model = app('workflow.resolver')->resolve($workflow);

                $model->handle($state);

                $redirect = $model->nextSession();

            } catch(StateException $e){

                $errors = $e->getErrors()->messages();

            } catch(QueryException $e){

                //todo log exception error
                \Message::addError("Database error.");

            } catch(\Exception $e){

                \Message::addError($e->getMessage());

            }

            $response = $redirect ?: \Redirect::back();

            if($errors) $response->withErrors($errors, $workflow . '_' . $state);

            return $response;
        });
    }

    /**
     * Register the workflow resolver
     */
    private function registerResolver()
    {
        $this->app->bindShared('workflow.resolver', function ($app){

            $resolver = new Services\Resolver;

            foreach($app->config['workflow'] as $alias => $config){

                $class = $app->workflow->register($alias, $config);

                $resolver->register($alias, $class);
            }

            return $resolver;
        });
    }
}