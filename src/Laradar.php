<?php

namespace Vcian\Laradar;

use Vcian\Laradar\Services\ArchitectureReport;
use Vcian\Laradar\Services\ArchitectureScanner;

class Laradar
{
    const VERSION = '1.0.0';

    public function __construct(protected ArchitectureScanner $scanner) {}

    public function discover(): ArchitectureReport
    {
        return $this->scanner->scan();
    }
}
