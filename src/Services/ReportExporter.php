<?php

namespace Hitesh\LaravelArchitectureDiscovery\Services;

use InvalidArgumentException;

class ReportExporter
{
    public function export(ArchitectureReport $report, string $format, string $path): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        match ($format) {
            'json' => file_put_contents($path, $report->toJson()),
            default => throw new InvalidArgumentException("Unsupported format: {$format}"),
        };
    }
}
