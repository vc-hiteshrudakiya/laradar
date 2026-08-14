<?php

namespace Hitesh\LaravelArchitectureDiscovery;

use Hitesh\LaravelArchitectureDiscovery\Services\ArchitectureReport;
use Hitesh\LaravelArchitectureDiscovery\Services\ArchitectureScanner;

class ArchitectureDiscovery
{
    const VERSION = '0.3.9';

    public function __construct(protected ArchitectureScanner $scanner) {}

    public function discover(): ArchitectureReport
    {
        return $this->scanner->scan();
    }
}
