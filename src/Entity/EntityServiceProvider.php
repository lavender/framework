<?php
namespace Lavender\Entity;

use Illuminate\Support\ServiceProvider;

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
        $this->package('lavender/entity', 'entity', realpath(__DIR__));

        $this->commands(['migrate.entity']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // register the installation collector (used in artisan commands)
        $this->registerInstaller();

        // register the migration creator (used in artisan commands)
        $this->registerCreator();

        // register artisan commands
        $this->registerCommands();

        $this->registerConfig();

        $this->registerAttributeRenderer();

        $this->app->booted(function (){

            $this->bindEntities();
        });
    }

    protected function registerAttributeRenderer()
    {
        $this->app->bindShared('attribute.renderer', function($app){
            return new Services\AttributeRenderer;
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

    protected function registerConfig()
    {
        // merge all entity.php config files
        $this->app['lavender.config']->merge(['entity']);

        $merge_entity = [
            'config' => 'entity',
            'default' => 'entity',
            'depth' => 1
        ];

        $merge_attributes = [
            'config' => 'entity',
            'default' => 'attribute',
            'index' => 'attributes',
            'depth' => 3
        ];

        $merge_relationships = [
            'config' => 'entity',
            'default' => 'relationship',
            'index' => 'relationships',
            'depth' => 3
        ];

        $this->app['lavender.config.defaults']->merge([$merge_entity, $merge_attributes, $merge_relationships]);
    }

    /**
     * Register core installation commands
     */
    protected function registerInstaller()
    {
        $this->app->installer->install('Install/update entities', function ($console){

            // Create first migration
            $console->call('migrate:entity', ['name' => 'install_lavender_' . time()]);

            // Run migrations
            $console->call('migrate');
        });
    }

    /**
     * Register artisan commands
     */
    protected function registerCommands()
    {
        $this->app->bindShared('migrate.entity', function ($app){

            $packagePath = $app['path.base'] . '/vendor';

            return new Commands\MigrateEntity($app['entity.creator'], $packagePath);
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

            return new Database\Migrations\Creator($app['files']);
        });
    }
}