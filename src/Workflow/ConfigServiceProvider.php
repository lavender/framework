<?php
namespace Lavender\Workflow;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
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
        return [];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/workflow', 'workflow_config', realpath(__DIR__));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['lavender.config']->merge(['workflow']);

        Blade::extend(function($view, $compiler)
        {
            $pattern = $compiler->createMatcher('workflow');

            return preg_replace($pattern, '$1<?php echo Workflow::make$2; ?>', $view);
        });
    }

}