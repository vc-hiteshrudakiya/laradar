<?php

namespace Hitesh\LaravelArchitectureDiscovery\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Hitesh\LaravelArchitectureDiscovery\ArchitectureDiscoveryServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ArchitectureDiscoveryServiceProvider::class,
        ];
    }
}
