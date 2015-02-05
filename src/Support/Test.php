<?php
namespace Lavender\Support;

use Illuminate\Foundation\Testing\TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Test extends TestCase
{

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        if(!$this->app){
            $this->refreshApplication();
        }
    }

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__.'/../../../../../bootstrap/start.php';
    }



}