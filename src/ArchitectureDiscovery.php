<?php

namespace Hitesh\LaravelArchitectureDiscovery;

use Hitesh\LaravelArchitectureDiscovery\Services\ArchitectureReport;
use Hitesh\LaravelArchitectureDiscovery\Services\ArchitectureScanner;

class ArchitectureDiscovery
{
    const VERSION = '0.1.0';

    public function __construct(protected ArchitectureScanner $scanner) {}

    public function discover(): ArchitectureReport
    {
        return $this->scanner->scan();
    }
}
