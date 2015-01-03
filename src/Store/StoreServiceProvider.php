<?php
namespace Lavender\Store;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\QueryException;
use Lavender\Entity\Database\QueryBuilder;

class StoreServiceProvider extends ServiceProvider
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
        return ['store', 'current.store'];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/store', 'store', realpath(__DIR__));

        $this->commands(['lavender.store']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->registerConfig();

        $this->registerInstaller();

        # $this->registerListeners();

        $this->app->booted(function (){

            $this->bootCurrentStore();
        });
    }

    protected function registerListeners()
    {

        \Event::listen('entity.query.select', function (QueryBuilder $query){

            $config = $query->config();

            if($config['scope'] == \Lavender::SCOPE_STORE){

                $query->where('store_id', '=', app('current.store')->id);
            } elseif($config['scope'] == \Lavender::SCOPE_DEPARTMENT){

                $query->where('store_id', '=', app('current.store')->id);

                $query->where('department_id', '=', app('current.department')->id);
            }
        });

        \Event::listen('entity.query.insert', function (QueryBuilder $query, &$values){

            $config = $query->config();

            if($config['scope'] == \Lavender::SCOPE_STORE){

                $values['store_id'] = app('current.store')->id;
            } elseif($config['scope'] == \Lavender::SCOPE_DEPARTMENT){

                $values['store_id'] = app('current.store')->id;

                $values['department_id'] = app('current.department')->id;
            }
        });

        \Event::listen('entity.creator.prepare', function (&$config){

            if($config['scope'] == \Lavender::SCOPE_STORE){

                $scope = ['store_id' => ['parent' => 'store']];

                merge_defaults($scope, 'attribute');

                $config['attributes'] += $scope;
            } elseif($config['scope'] == \Lavender::SCOPE_DEPARTMENT){

                $scope = [
                    'store_id' => ['parent' => 'store'],
                    'department_id' => ['parent' => 'department'],
                ];

                merge_defaults($scope, 'attribute');

                $config['attributes'] += $scope;
            }
        });
    }

    protected function registerInstaller()
    {
        $this->app['lavender.installer']->update('Install default store', function ($console){

            // If a default store doesnt exist, create it now
            if(!$this->app->bound('current.store') || !app('current.store')){

                $console->call('lavender:store', ['--default' => true]);

                $this->bootCurrentStore();
            }
        });
    }

    protected function registerConfig()
    {
        $this->app['lavender.config']->merge(['store']);

        $this->app['lavender.theme.config']->merge(['store']);
    }

    protected function registerCommands()
    {
        $this->app->bind('lavender.store', function (){
            return new Commands\CreateStore;
        });
    }

    public function bootCurrentStore()
    {
        try{
            // Find the default store
            $store = app('store')->where('default', '=', true)->first();

            // Register the current store object
            $this->app->singleton('current.store', function () use ($store){ return $store; });
        } catch(QueryException $e){

            // missing core tables
            if(!\App::runningInConsole()) throw new \Exception("Lavender not installed.");
        } catch(\Exception $e){

            // something went wrong
            if(!\App::runningInConsole()) throw new \Exception($e->getMessage());
        }
    }
}

