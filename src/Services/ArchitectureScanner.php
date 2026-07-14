<?php

namespace Hitesh\LaravelArchitectureDiscovery\Services;

class ArchitectureScanner
{
    public function __construct(protected array $analyzers) {}

    public function scan(): ArchitectureReport
    {
        $startTime   = microtime(true);
        $startMemory = memory_get_usage(true);

        $report = new ArchitectureReport(
            config('app.name', 'Laravel Application'),
            base_path(),
        );

        foreach (['models', 'controllers', 'routes'] as $type) {
            if (!isset($this->analyzers[$type])) {
                continue;
            }

            $result = $this->analyzers[$type]->analyze();

            $addItem = match ($type) {
                'models'      => fn($item) => $report->addModel($item),
                'controllers' => fn($item) => $report->addController($item),
                'routes'      => fn($item) => $report->addRoute($item),
            };

            foreach ($result['items'] as $item) {
                $addItem($item);
            }

            foreach ($result['errors'] as $error) {
                $report->addError($error);
            }
        }

        $report->setPerformance(
            (microtime(true) - $startTime) * 1000,
            (memory_get_usage(true) - $startMemory) / 1024 / 1024,
        );

        return $report;
    }
}
