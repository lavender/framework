<?php
namespace Lavender\Catalog;

use Illuminate\Support\ServiceProvider;

class CatalogServiceProvider extends ServiceProvider
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
        $this->package('lavender/catalog', 'catalog', realpath(__DIR__));

        $this->commands(['lavender.category.creator']);
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

        $this->app->booted(function (){

            $this->registerRoutes();
        });
    }

    private function registerCommands()
    {
        $this->app->bind('lavender.category.creator', function (){
            return new Commands\CreateCategory;
        });
    }

    private function registerInstaller()
    {
        $this->app['lavender.installer']->update('Install root category', function ($console){

            // If a root category doesn't exist, create it now
            if(!app('current.store')->root_category){

                $console->call('lavender:category', ['--store' => app('current.store')->id]);

                // Register the new store object
                $store = app('store')->find(app('current.store')->id);

                $this->app->singleton('current.store', function () use ($store){ return $store; });
            }
        });
    }

    private function registerRoutes()
    {
        // Product view pages
        \Route::get(\Config::get('store.product_url') . '/{product}', function ($product){

            $url = \Config::get('store.product_url') . '/' . $product;

            $product = app('product')->findByAttribute('url', $url);

            return $this->app->view->make('catalog.product.view')
                ->withProduct($product);
        });

        // Category view pages
        \Route::get(\Config::get('store.category_url') . '/{category}', function ($category){

            $url = \Config::get('store.category_url') . '/' . $category;

            $category = app('category')->findByAttribute('url', $url);

            return $this->app->view->make('catalog.category.view')
                ->withCategory($category)
                ->withProducts($category->products()->paginate(\Config::get('store.product_count')));
        });
    }
}