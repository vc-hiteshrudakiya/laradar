<?php

namespace Vcian\Laradar\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Vcian\Laradar\LaradarServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaradarServiceProvider::class,
        ];
    }
}
