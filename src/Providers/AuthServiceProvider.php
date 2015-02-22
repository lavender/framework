<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Auth\Account\Resolver;
use Lavender\Auth\Manager;
use Lavender\Auth\Password;
use Lavender\Auth\Throttle;

class AuthServiceProvider extends ServiceProvider
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
            'auth',
            'auth.driver',
            'account.service',
            'account.password',
            'account.throttle',
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuthenticator();

        $this->registerAccountService();

        $this->registerPasswordService();

        $this->registerThrottleService();

        $this->registerUserResolver();

        $this->registerRequestRebindHandler();
    }

    /**
     * Re-bind the auth binding to support multiple authentication types.
     */
    private function registerAuthenticator()
    {
        $this->app->singleton('auth', function ($app){

            $app['auth.loaded'] = true;

            return new Resolver($app);
        });

        $this->app->singleton('auth.driver', function($app)
        {
            dd("?");
            return $app['auth'];//->customer()->driver();
        });
    }

    /**
     * Register a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerUserResolver()
    {
        $this->app->bind('Illuminate\Contracts\Auth\Authenticatable', function($app)
        {
            return $app['auth'];//->user();
        });
    }

    /**
     * Register a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerRequestRebindHandler()
    {
        $this->app->rebinding('request', function($app, $request)
        {
            $request->setUserResolver(function() use ($app)
            {
                return $app['auth'];//->user();
            });
        });
    }

    /**
     * Register the service used by the Account facade.
     */
    private function registerAccountService()
    {
        $this->app->singleton('account.service', function ($app){

            return new Manager($app);
        });
    }

    /**
     * This service abstracts all user password management related methods
     */
    private function registerPasswordService()
    {
        $this->app->singleton('account.password', function ($app){
            return new Password($app);
        });
    }

    /**
     * This service Throttles logins after too many failed attempts.
     * This is a secure measure in order to avoid brute force attacks.
     */
    private function registerThrottleService()
    {
        $this->app->singleton('account.throttle', function ($app){
            return new Throttle();
        });
    }

}