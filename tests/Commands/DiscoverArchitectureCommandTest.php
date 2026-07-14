<?php

namespace Hitesh\LaravelArchitectureDiscovery\Tests\Commands;

use Hitesh\LaravelArchitectureDiscovery\Tests\TestCase;

class DiscoverArchitectureCommandTest extends TestCase
{
    public function test_command_runs_successfully(): void
    {
        $this->artisan('architecture:discover')
            ->assertExitCode(0);
    }

    public function test_command_displays_output(): void
    {
        $this->artisan('architecture:discover')
            ->expectsOutputToContain('Scanning application architecture')
            ->expectsOutputToContain('Models found')
            ->expectsOutputToContain('Controllers found')
            ->expectsOutputToContain('Routes found')
            ->expectsOutputToContain('Architecture discovered successfully')
            ->assertExitCode(0);
    }
}
