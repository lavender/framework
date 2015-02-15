<?php
namespace Lavender\Services;

use Illuminate\Support\Facades\View;

class PageRouter
{

    /**
     * @param $path
     * @param array $route
     */
    public function route($path, array $route)
    {
        $route = array_merge([
            'controller' => null,
            'action'     => null,
            'layout'     => null,
            'method'     => 'get',
            'before'     => null,
            'with'       => []
        ], $route);

        if($route['layout']){

            $callback = function () use ($route){

                return View::make($route['layout']);
            };

            $this->_route($route['method'], $path, $callback, $route['before']);

        } elseif($route['controller'] && $route['action']){

            $action = sprintf("%s@%s", $route['controller'], $route['action']);

            $this->_route($route['method'], $path, ['uses' => $action], $route['before']);
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
    protected function _route($method, $path, $callback, $before)
    {
        \Route::$method($path, $callback)->before($before);
    }

}