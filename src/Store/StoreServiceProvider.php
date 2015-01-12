<?php
namespace Lavender\Store;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\QueryException;
use Lavender\Entity\Database\QueryBuilder;
use Lavender\Store\Database\Store;
use Lavender\Store\Facades\Scope;

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
        return ['store'];
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
        $this->registerStore();

        $this->registerCommands();

        $this->registerConfig();

        $this->registerInstaller();

        $this->registerListeners();

        $this->app->booted(function (){

            $this->bootCurrentStore();

            $this->app->theme->booted(function (){

                $this->mergeConfig();

            });
        });
    }

    private function registerStore()
    {
        $this->app->bindShared('store', function (){
            return new Store();
        });
    }
    protected function registerListeners()
    {

       /* \Event::listen('entity.query.select', function (QueryBuilder $query){

            $config = $query->config();

            if($config['scope'] == Scope::IS_STORE){

                $query->where('store_id', '=', app('current.store')->id);
            } elseif($config['scope'] == Scope::IS_DEPARTMENT){

                $query->where('store_id', '=', app('current.store')->id);

                $query->where('department_id', '=', app('current.department')->id);
            }
        });

        \Event::listen('entity.query.insert', function (QueryBuilder $query, &$values){

            $config = $query->config();

            if($config['scope'] == Scope::IS_STORE){

                $values['store_id'] = app('current.store')->id;
            } elseif($config['scope'] == Scope::IS_DEPARTMENT){

                $values['store_id'] = app('current.store')->id;

                $values['department_id'] = app('current.department')->id;
            }
        });

        \Event::listen('entity.creator.prepare', function (&$config){

            if($config['scope'] == Scope::IS_STORE){

                $scope = ['store_id' => ['parent' => 'store']];

                merge_defaults($scope, 'attribute');

                $config['attributes'] += $scope;
            } elseif($config['scope'] == Scope::IS_DEPARTMENT){

                $scope = [
                    'store_id' => ['parent' => 'store'],
                    'department_id' => ['parent' => 'department'],
                ];

                merge_defaults($scope, 'attribute');

                $config['attributes'] += $scope;
            }
        });*/
    }

    protected function registerInstaller()
    {
        $this->app->installer->update('Install default store', function ($console){

            // If a default store doesnt exist, create it now
            if(!$this->app->store->id){

                $console->call('lavender:store', ['--default' => true]);

                $this->bootCurrentStore();
            }
        });
    }

    protected function registerConfig()
    {
        $this->app['lavender.config']->merge(['store']);
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
            $store = $this->app->store->where('default', '=', true)->first();

            // Register the current store object
            $this->app->store->booting($store);

            $this->app->store = $store;

        } catch(QueryException $e){

            // missing core tables
            if(!\App::runningInConsole()) throw new \Exception("Lavender not installed.");
        } catch(\Exception $e){

            // something went wrong
            if(!\App::runningInConsole()) throw new \Exception($e->getMessage());
        }
    }

    public function mergeConfig()
    {
        $config = $this->app->store->config->all();

        foreach($config as $item){

            $this->app['config']->set('store.'.$item->key, $item->value);

        }
    }
}

