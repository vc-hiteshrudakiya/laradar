<?php

namespace Vcian\Laradar\Tests\Commands;

use Vcian\Laradar\Tests\TestCase;

class LaradarCommandTest extends TestCase
{
    public function test_command_runs_successfully(): void
    {
        $this->artisan('laradar:scan')
            ->assertExitCode(0);
    }

    public function test_command_displays_output(): void
    {
        $this->artisan('laradar:scan')
            ->expectsOutputToContain('Scanning Project')
            ->expectsOutputToContain('Models')
            ->expectsOutputToContain('Controllers')
            ->expectsOutputToContain('Routes')
            ->expectsOutputToContain('Completed Successfully')
            ->assertExitCode(0);
    }

    public function test_command_displays_score(): void
    {
        $this->artisan('laradar:scan')
            ->expectsOutputToContain('Architecture Score')
            ->assertExitCode(0);
    }

    public function test_command_rejects_invalid_format(): void
    {
        $this->artisan('laradar:scan --format=xml')
            ->assertExitCode(1);
    }

    public function test_command_accepts_html_format(): void
    {
        $this->artisan('laradar:scan --format=html')
            ->assertExitCode(0);
    }

    public function test_command_accepts_markdown_format(): void
    {
        $this->artisan('laradar:scan --format=markdown')
            ->assertExitCode(0);
    }

    public function test_command_accepts_json_format(): void
    {
        $this->artisan('laradar:scan --format=json')
            ->assertExitCode(0);
    }
}
