<?php
namespace Lavender\Support;

use Illuminate\Foundation\Testing\TestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class Test extends TestCase
{

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