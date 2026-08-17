<?php

namespace Vcian\Laradar;

use Vcian\Laradar\Services\ArchitectureReport;
use Vcian\Laradar\Services\ArchitectureScanner;

class ArchitectureDiscovery
{
    const VERSION = '0.3.9';

    public function __construct(protected ArchitectureScanner $scanner) {}

    public function discover(): ArchitectureReport
    {
        return $this->scanner->scan();
    }
}
