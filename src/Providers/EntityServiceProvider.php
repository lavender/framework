<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Commands\MigrateEntity;
use Lavender\Database\Migrations\Creator;
use Lavender\Services\AttributeRenderer;

class EntityServiceProvider extends ServiceProvider
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
        return ['migrate.entity', 'attribute.renderer'];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands(['migrate.entity']);

        $this->bindEntities();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // register the migration creator (used in artisan commands)
        $this->registerCreator();

        // register artisan commands
        $this->registerCommands();

        $this->registerAttributeRenderer();

    }

    protected function registerAttributeRenderer()
    {
        $this->app->bindShared('attribute.renderer', function($app){
            return new AttributeRenderer();
        });
    }

    /**
     * Bind all registered entities to the application so we can easily
     * instantiate them anywhere we need them.
     */
    protected function bindEntities()
    {
        $entities = $this->app->config['entity'];

        foreach($entities as $e => $config){

            $this->app->bind("entity.$e", function ($app, $default) use ($config){

                return new $config['class'];

            });
        }
    }


    /**
     * Register artisan commands
     */
    protected function registerCommands()
    {
        $this->app->bindShared('migrate.entity', function ($app){

            $packagePath = $app['path.base'] . '/vendor';

            return new MigrateEntity($app['entity.creator'], $packagePath);
        });
    }

    /**
     * Register the migration creator.
     *
     * @return void
     */
    protected function registerCreator()
    {
        $this->app->bindShared('entity.creator', function ($app){

            return new Creator($app['files']);
        });
    }
}