<?php
namespace Lavender\View;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\QueryException;
use Lavender\View\Database\Theme;

class ThemeServiceProvider extends ServiceProvider
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
        return ['theme', 'theme.creator'];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/view', 'theme', realpath(__DIR__));

        $this->commands(['theme.creator']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTheme();

        $this->registerCommands();

        $this->registerConfig();

        $this->registerInstaller();

        $this->app->booted(function (){

            $this->bootCurrentTheme();

        });
    }

    private function registerTheme()
    {
        $this->app->bindShared('theme', function($app){
            return new Theme();
        });
    }

    /**
     * Register artisan commands
     */
    private function registerCommands()
    {
        $this->app->bind('theme.creator', function (){
            return new Commands\CreateTheme;
        });
    }

    /**
     * Register view configs
     */
    private function registerConfig()
    {
        $this->app['lavender.config']->merge(['composers', 'layout', 'routes', 'filters']);

        $this->app['lavender.theme.config']->merge(['composers', 'layout', 'routes', 'filters']);

        $merge_routes = [
            'config' => 'routes',
            'default' => 'routes',
            'depth' => 1
        ];

        $merge_layout = [
            'config' => 'layout',
            'default' => 'layout',
            'depth' => 3
        ];

        $this->app['lavender.config.defaults']->merge([$merge_routes, $merge_layout]);
    }

    /**
     * Register view installer
     */
    private function registerInstaller()
    {
        $this->app->installer->update('Install default theme', function ($console){

            // If a default theme doesnt exist, create it now
            if(!$this->app->theme->id){

                $console->call('lavender:theme', ['--store' => $this->app->store->id]);

                $this->bootCurrentTheme();
            }
        });
    }

    /**
     * Match user session to initialize $theme
     *
     * @throws \Exception
     * @return void
     */
    private function bootCurrentTheme()
    {
        try{
            // Load the theme assigned to the current store
            $theme = $this->app->store->theme;

            // Now that we have our theme loaded, lets collect the fallbacks
            $theme->fallbacks = $this->themes($theme);

            // Register the theme object with the theme fallbacks
            $this->app->theme->booting($theme);

            $this->app->theme = $theme;

            // note: we boot the theme's callbacks prior to registering
            // the layouts, composers, routes, and filters.

            // Override Laravel's view.finder to support theme fallbacks.
            $this->registerViewFinder();

            // Now let's register our view composers
            $this->registerComposers();

            // Inject views into our layouts
            $this->injectLayoutViews();

            // Register filters that are used for routes
            $this->registerFilters();

            // Finally we can load our routes and filters so the app can finish booting.
            $this->registerRoutes();

        } catch(QueryException $e){

            // missing core tables
            if(!\App::runningInConsole()) throw new \Exception("Lavender not installed.");
        } catch(\Exception $e){

            // something went wrong
            if(!\App::runningInConsole()) throw new \Exception($e->getMessage());
        }
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    private function registerViewFinder()
    {
        $viewFinder = $this->app->make('view.finder');

        $paths = $this->app->config['view.paths'];

        $hints = $viewFinder->getHints();

        foreach($hints as $module_paths){

            $paths = array_merge($paths, $module_paths);
        }

        foreach($this->app->theme->fallbacks as $fallback){

            foreach($paths as $path){

                $viewFinder->addLocation($path . '/' . $fallback);
            }
        }
    }

    /**
     * Register all composers
     *
     * @return void
     */
    private function registerComposers()
    {
        foreach($this->app->config['composers'] as $layout => $composer){

            $this->app->view->composer($layout, $composer);
        }
    }

    /**
     * Injects layout views.
     */
    private function injectLayoutViews()
    {
        foreach($this->app->config['layout'] as $viewName => $sections){

            $this->app->view->composer($viewName, function ($view) use ($sections){

                $this->app['layout.injector']->inject($sections);
            });
        }
    }

    /**
     * Define a theme's routes and route filters.
     *
     * @return void
     */
    private function registerFilters()
    {
        foreach($this->app->config['filters'] as $filter => $callback){

            \Route::filter($filter, $callback);
        }
    }

    /**
     * Define a theme's routes and route filters.
     *
     * @return void
     */
    private function registerRoutes()
    {
        foreach($this->app->config['routes'] as $path => $route){

            if($route['layout']){

                $this->route($route['method'], $path, ['before' => $route['before'], function () use ($route){

                    return $this->app->view->make($route['layout']);
                }]);
            } elseif($route['controller'] && $route['action']){

                $action = sprintf("%s@%s", $route['controller'], $route['action']);

                $this->route($route['method'], $path, ['before' => $route['before'], 'uses' => $action]);
            }
        }
    }



    /**
     * Register Route
     *
     * @param string $method get|post
     * @param string $path uri segment
     * @param mixed $callback array|Closure
     * @return void
     */
    protected function route($method, $path, $callback)
    {
        if($method == 'post'){

            \Route::post($path, $callback);
        } else{

            \Route::get($path, $callback);
        }
    }

    /**
     * Merge inherited theme routes
     * @param Theme $theme
     * @internal param int $theme_id
     * @return array
     */
    protected function themes(Theme $theme)
    {
        $themes[] = $theme->code;

        $parent = $theme->parent;

        if($parent->id != $theme->id){

            $themes = array_merge(
                $themes,
                $this->themes($parent)
            );
        }

        return $themes;
    }
}

