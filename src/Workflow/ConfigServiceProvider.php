<?php
namespace Lavender\Workflow;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Lavender\Workflow\Exceptions\StateException;
use Lavender\Support\Facades\Workflow;

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

        });

        Blade::extend(function($view, $compiler)
        {
            $pattern = $compiler->createMatcher('workflow');

            return preg_replace($pattern, '$1<?php echo Workflow::make$2; ?>', $view);
        });
    }

    /**
     * Merge workflow config
     */
    private function registerConfig()
    {
        $this->app['lavender.config']->merge(['workflow']);
    }

    /**
     * Register the workflow post requests
     * todo better routing
     */
    private function registerRoutes()
    {
        $baseUrl = $this->app->config['store.workflow_base_url'];

        Route::post($baseUrl.'/{workflow}/{state}', function ($workflow, $state){

            $errors = null;

            try{

                $model = Workflow::make($workflow);

                $model->next($state, Input::all());

            } catch(StateException $e){

                $errors = $e->getErrors()->messages();

            } catch(QueryException $e){

                //todo log exception
                $errors = "Database error.";

            } catch(\Exception $e){

                //todo log exception
                $errors = $e->getMessage();

            }

            $response = Workflow::response();

            if($errors) $response->withErrors($errors, $workflow . '_' . $state);

            return $response;

        });

        Route::post($baseUrl.'/{workflow}/{state}/{entity}/{id}', function ($workflow, $state, $entity, $id){
            $errors = null;

            try{

                $model = Workflow::make($workflow)
                    ->with('entity', entity($entity)->find($id));

                $model->next($state, Input::all());

            } catch(StateException $e){

                $errors = $e->getErrors()->messages();

            } catch(QueryException $e){

                //todo log exception
                $errors = "Database error.";

            } catch(\Exception $e){

                //todo log exception
                $errors = $e->getMessage();

            }

            $response = Workflow::response();

            if($errors) $response->withErrors($errors, $workflow . '_' . $state);

            return $response;
        });
    }

}