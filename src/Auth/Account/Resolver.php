<?php
namespace Lavender\Auth\Account;

use Illuminate\Foundation\Application;

class Resolver
{
    protected $config;

    protected $providers = array();

    public function __construct(Application $app)
    {
        $this->config = $app->config['auth.account'];

        foreach($this->config as $key => $config){

            $this->providers[$key] = new Manager($app, $key, $config);
        }
    }

    public function __call($name, $arguments = array())
    {
        if(array_key_exists($name, $this->providers)){

            return $this->providers[$name];
        }
    }
}
