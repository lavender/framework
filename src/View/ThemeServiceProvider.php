<?php
namespace Lavender\View;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\QueryException;
use Illuminate\View\Factory;
use Lavender\View\Database\Theme;
use Lavender\View\Facades\Layout;
use Lavender\Workflow\Interfaces\WorkflowInterface;

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
        return ['theme', 'current.theme', 'lavender.theme.creator'];
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('lavender/theme', 'theme', realpath(__DIR__));

        $this->commands(['lavender.theme.creator']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->registerConfig();

        $this->registerInstaller();

        $this->app->booted(function (){

            $this->bootCurrentTheme();
        });
    }


    /**
     * Register artisan commands
     */
    private function registerCommands()
    {
        $this->app->bind('lavender.theme.creator', function (){
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
        $this->app['lavender.installer']->update('Install default theme', function ($console){

            // If a default theme doesnt exist, create it now
            if(!$this->app->bound('current.theme')){

                $console->call('lavender:theme', ['--store' => app('current.store')->id]);

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
            $store = app('current.store');

            // Set the current theme
            $theme = $store->theme;

            // Now that we have our theme loaded, lets collect the fallbacks
            $theme->fallbacks = $this->themes($theme);

            // Register the theme object with the theme fallbacks
            $this->app->singleton('current.theme', function () use ($theme){ return $theme; });

            // Fire an event to let other modules know the theme is registered (eg. merge theme configs)
            \Event::fire('lavender.theme', [$theme]);

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

        $theme = $this->app->make('current.theme');

        foreach($theme->fallbacks as $fallback){

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

                $this->inject($view->getFactory(), $sections);
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
     * Inject views into layouts
     *
     * @param Factory $factory
     * @param array $sections
     */
    private function inject(Factory $factory, array $sections)
    {
        foreach($sections as $sectionName => $children){

            // Sort from highest to lowest 'position' in order to render
            // the first child first by declaring it last.
            sort_children($children);

            $children = array_reverse($children);

            foreach($children as $childName => $childConfig){

                $html = null;
                // todo remove (path hints)
                #$viewData = [
                #    'Layout' => $childName,
                #    'Section' => $sectionName
                #];
                //
                if($childConfig['script']){

                    $html = \HTML::script($childConfig['script']);

                } elseif($childConfig['meta']){

                    $html = \HTML::meta($childConfig['meta']);

                } elseif($childConfig['style']){

                    $html = \HTML::style($childConfig['style']);

                } elseif($childConfig['layout'] instanceof \Closure){
                    // todo remove (path hints)
                    #$viewData['Type'] = "Closure";
                    //

                    $html = $childConfig['layout']();

                    if($html instanceof WorkflowInterface){
                        //todo remove (path hints)
                        #unset($viewData['Type']);
                        #$viewData['Workflow'] = get_class($html);
                        //

                        $html = $html->render();
                    }
                } elseif($factory->exists($childConfig['layout'])){

                    $view = $factory->make($childConfig['layout']);
                    // todo remove (path hints)
                    #$viewData['Template'] = str_replace(base_path(), null, $view->getPath());
                    //

                    $html = $view->render();
                } elseif($childConfig['config']){

                    $html = $this->app->config[$childConfig['config']];
                } else{

                    continue;
                }

                if($html){

                    // todo remove (path hints)
                    #if(stristr($sectionName, 'head.') === false){
                    #$viewData = implode("\n",array_map(function($k,$v){return $k.":\n\t".$v;}, array_keys($viewData), $viewData));
                    #$html = "<div class='template-hint' style='border:1px solid red;' title='{$viewData}'>{$html}</div>";
                    #}
                    //

                    $factory->inject(
                        $sectionName,
                        $childConfig['mode'] == Layout::REPLACE ?
                            $html : '@parent' . PHP_EOL . $html
                    );
                }
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

