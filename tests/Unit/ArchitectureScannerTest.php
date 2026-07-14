<?php

namespace Hitesh\LaravelArchitectureDiscovery\Tests\Unit;

use Hitesh\LaravelArchitectureDiscovery\Services\ArchitectureReport;
use Hitesh\LaravelArchitectureDiscovery\Services\ArchitectureScanner;
use Hitesh\LaravelArchitectureDiscovery\Tests\TestCase;

class ArchitectureScannerTest extends TestCase
{
    public function test_scanner_returns_report_instance(): void
    {
        $scanner = $this->app->make(ArchitectureScanner::class);

        $report = $scanner->scan();

        $this->assertInstanceOf(ArchitectureReport::class, $report);
    }

    public function test_scanner_report_has_required_keys(): void
    {
        $scanner = $this->app->make(ArchitectureScanner::class);

        $data = $scanner->scan()->getReport();

        $this->assertArrayHasKey('project', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('models', $data);
        $this->assertArrayHasKey('controllers', $data);
        $this->assertArrayHasKey('routes', $data);
    }
}
