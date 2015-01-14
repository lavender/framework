<?php
namespace Lavender\Account;

use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
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
        return array();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/account', 'account', realpath(__DIR__));
        $this->commands(['admin.creator']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->registerInstaller();
    }

    /**
     * Register view installer
     */
    private function registerInstaller()
    {
        $this->app->installer->update('Install admin account', function ($console){

            // If a default theme doesn't exist, create it now
            if(!entity('admin')->all()){

                $console->call('lavender:admin');

            }
        });
    }
    /**
     * Register artisan commands
     */
    private function registerCommands()
    {
        $this->app->bind('admin.creator', function (){
            return new Commands\CreateAdmin;
        });
    }
}