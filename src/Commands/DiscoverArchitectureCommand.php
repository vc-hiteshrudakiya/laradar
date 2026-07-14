<?php

namespace Hitesh\LaravelArchitectureDiscovery\Commands;

use Illuminate\Console\Command;
use Hitesh\LaravelArchitectureDiscovery\ArchitectureDiscovery;
use Hitesh\LaravelArchitectureDiscovery\Services\ReportExporter;

class DiscoverArchitectureCommand extends Command
{
    protected $signature = 'architecture:discover
                            {--format=json : Output format (json)}
                            {--output= : Custom output file path}';

    protected $description = 'Discover and document Laravel application architecture';

    public function handle(ArchitectureDiscovery $discovery, ReportExporter $exporter): int
    {
        $this->info('Scanning application architecture...');
        $this->newLine();

        $report = $discovery->discover();
        $data   = $report->getReport();

        $summary = $data['summary'];
        $this->line('<fg=green>Models found:</>      ' . $summary['models']);
        $this->line('<fg=green>Controllers found:</> ' . $summary['controllers']);
        $this->line('<fg=green>Routes found:</>      ' . $summary['routes']);

        $this->newLine();

        $format = $this->option('format') ?: 'json';
        $output = $this->option('output') ?: storage_path('architecture/report.' . $format);

        $exporter->export($report, $format, $output);

        $this->info('Architecture discovered successfully.');
        $this->line('<fg=yellow>Report saved: ' . $output . '</>');

        return self::SUCCESS;
    }
}
