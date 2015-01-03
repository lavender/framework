<?php
namespace Lavender\Account\Service;

use Illuminate\Foundation\Application;

class Manager
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    protected $entities;

    function __construct(Application $app)
    {
        $this->app = $app;

        $this->entities = array_keys($app->config['auth.account']);
    }

    /**
     * Load account by name.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws \Exception
     */
    function __call($method, $parameters)
    {
        if(in_array($method, $this->entities)){

            return $this->app['auth']->$method();
        }

        throw new \Exception("Wrong model specified in config/auth.php", 639);
    }
}
