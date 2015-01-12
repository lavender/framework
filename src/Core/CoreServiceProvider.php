<?php
namespace Lavender\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
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
        return ['bootloader', 'installer'];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/core', 'core');

        $this->commands(['lavender.install']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBootloader();

        $this->registerInstaller();

        $this->registerCommands();
    }

    /**
     * Register the lavender bootloader
     */
    private function registerBootloader()
    {
        $this->app->bindShared('bootloader', function($app){
            return new Services\Bootloader;
        });
    }


    /**
     * Register core installation commands
     */
    private function registerInstaller()
    {
        $this->app->bindShared('installer', function ($app){
            return new Services\Installer();
        });
    }

    /**
     * Register artisan commands
     */
    protected function registerCommands()
    {
        $this->app->bindShared('lavender.install', function ($app){

            return new Commands\InstallLavender();
        });
    }
}

