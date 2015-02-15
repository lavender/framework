<?php
namespace Lavender\Auth;

use Illuminate\Foundation\Application;

class Manager
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected $app;

    protected $accounts;

    function __construct(Application $app)
    {
        $this->app = $app;

        $this->accounts = array_keys($app->config['auth.account']);
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
        if(in_array($method, $this->accounts)){

            return $this->app['auth']->$method();
        }

        throw new \Exception("Wrong model specified in config/auth.php", 639);
    }
}
