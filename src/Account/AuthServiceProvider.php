<?php
namespace Lavender\Account;

use Illuminate\Support\ServiceProvider;

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
            'account.service',
            'account.password',
            'account.throttle',
            'account.validator'
        ];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuth();

        $this->registerAccountService();

        $this->registerValidatorService();

        $this->registerPasswordService();

        $this->registerThrottleService();
    }

    /**
     * Re-bind the auth binding to support multiple authentication types.
     */
    private function registerAuth()
    {
        $this->app->bindShared('auth', function ($app){

            $app['auth.loaded'] = true;

            return new Services\Auth\Resolver($app);
        });
    }

    /**
     * Register the service used by the Account facade.
     */
    private function registerAccountService()
    {
        $this->app->bindShared('account.service', function ($app){

            return new Services\Manager($app);
        });
    }

    /**
     * This service abstracts all user password management related methods
     */
    private function registerPasswordService()
    {
        $this->app->bindShared('account.password', function ($app){
            return new Services\Password($app);
        });
    }

    /**
     * This service Throttles logins after too many failed attempts.
     * This is a secure measure in order to avoid brute force attacks.
     */
    private function registerThrottleService()
    {
        $this->app->bindShared('account.throttle', function ($app){
            return new Services\Throttle;
        });
    }

    /**
     * This service validates the given user.
     */
    private function registerValidatorService()
    {
        $this->app->bindShared('account.validator', function ($app){
            return new Services\Validator;
        });
    }
}