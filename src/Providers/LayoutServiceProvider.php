<?php
namespace Lavender\Providers;

use Illuminate\Support\ServiceProvider;
use Lavender\Services\LayoutInjector;

class LayoutServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Wait until app is booted
        $this->app->booted(function(){

            // Inject views into our layouts
            foreach(config('layout') as $viewName => $sections){

                $this->app->view->composer($viewName, function ($view) use ($sections){

                    $injector = new LayoutInjector;

                    $injector->inject($sections);
                });
            }

        });
    }

}