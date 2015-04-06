<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Http\FormRequest;
use Symfony\Component\HttpFoundation\Request;

class FormRequestServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;



    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->listen('router.matched', function(){

            $this->app->resolving(function(FormRequest $request, $app){

                $request->setRequest($app['request']);

            });

        });
    }

}