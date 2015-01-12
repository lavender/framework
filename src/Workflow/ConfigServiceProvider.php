<?php
namespace Lavender\Workflow;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Lavender\Workflow\Exceptions\StateException;
use Lavender\Workflow\Facades\Workflow;

class ConfigServiceProvider extends ServiceProvider
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
        return [];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/workflow', 'config', realpath(__DIR__));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        $this->app->booted(function(){

            $this->registerRoutes();

            $this->app->theme->booted(function(){

                $this->registerListeners();

            });

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

    public function registerListeners()
    {
        foreach($this->app->config['workflow'] as $workflow => $states){

            foreach($states as $state => $config){

                $this->app->events->listen("workflow.{$workflow}.{$state}", function($view) use ($config){

                    foreach($config['before'] as $before => $filter){

                        if($model = new $filter['class']) $model->handle($view);

                    }
                });

            }
        }
    }

    /**
     * Register the workflow post requests
     */
    private function registerRoutes()
    {
        $baseUrl = $this->app->config['store.workflow_base_url'];

        Route::post($baseUrl.'/{workflow}/{state}', function ($workflow, $state){

            $errors = null;

            $response = Redirect::back();

            try{

                $model = Workflow::make($workflow);

                $model->handle($state, Input::all());

                $response = Workflow::next($workflow, $state);

            } catch(StateException $e){

                $errors = $e->getErrors()->messages();

            } catch(QueryException $e){

                //todo log exception error
                \Message::addError("Database error.");

            } catch(\Exception $e){

                \Message::addError($e->getMessage());

            }

            if($errors) $response->withErrors($errors, $workflow . '_' . $state);

            return $response;

        });
    }

}