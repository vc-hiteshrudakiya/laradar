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
            ->expectsOutputToContain('Scanning Project')
            ->expectsOutputToContain('Models')
            ->expectsOutputToContain('Controllers')
            ->expectsOutputToContain('Routes')
            ->expectsOutputToContain('Completed Successfully')
            ->assertExitCode(0);
    }

    public function test_command_displays_score(): void
    {
        $this->artisan('architecture:discover')
            ->expectsOutputToContain('Architecture Score')
            ->assertExitCode(0);
    }

    public function test_command_rejects_invalid_format(): void
    {
        $this->artisan('architecture:discover --format=xml')
            ->assertExitCode(1);
    }

    public function test_command_accepts_html_format(): void
    {
        $this->artisan('architecture:discover --format=html')
            ->assertExitCode(0);
    }

    public function test_command_accepts_markdown_format(): void
    {
        $this->artisan('architecture:discover --format=markdown')
            ->assertExitCode(0);
    }

    public function test_command_accepts_json_format(): void
    {
        $this->artisan('architecture:discover --format=json')
            ->assertExitCode(0);
    }
}
