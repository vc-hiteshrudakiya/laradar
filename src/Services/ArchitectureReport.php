<?php

namespace Hitesh\LaravelArchitectureDiscovery\Services;

use Hitesh\LaravelArchitectureDiscovery\ArchitectureDiscovery;

class ArchitectureReport
{
    private array $models = [];
    private array $controllers = [];
    private array $routes = [];
    private array $errors = [];
    private array $performance = [];

    public function __construct(
        private string $projectName,
        private string $projectBasePath,
    ) {}

    public function setPerformance(float $executionTimeMs, float $memoryUsageMb): void
    {
        $this->performance = [
            'execution_time_ms' => round($executionTimeMs, 2),
            'memory_usage_mb'   => round($memoryUsageMb, 2),
        ];
    }

    public function addModel(array $model): void
    {
        $this->models[] = $model;
    }

    public function addController(array $controller): void
    {
        $this->controllers[] = $controller;
    }

    public function addRoute(array $route): void
    {
        $this->routes[] = $route;
    }

    public function addError(array $error): void
    {
        $this->errors[] = $error;
    }

    public function getReport(): array
    {
        return [
            'package_version' => ArchitectureDiscovery::VERSION,
            'laravel_version' => app()->version(),
            'php_version'     => PHP_VERSION,
            'generated_at'    => now()->toIso8601String(),
            'project'         => [
                'name'      => $this->projectName,
                'base_path' => $this->projectBasePath,
            ],
            'performance'   => $this->performance,
            'summary'       => [
                'models'               => count($this->models),
                'controllers'          => count($this->controllers),
                'routes'               => count($this->routes),
                'relationship_summary' => $this->buildRelationshipSummary(),
            ],
            'route_summary' => $this->buildRouteSummary(),
            'models'        => $this->models,
            'controllers'   => $this->controllers,
            'routes'        => $this->routes,
            'errors'        => $this->errors,
        ];
    }

    private function buildRelationshipSummary(): array
    {
        $summary = [];

        foreach ($this->models as $model) {
            foreach ($model['relationships'] ?? [] as $rel) {
                $summary[$rel['type']] = ($summary[$rel['type']] ?? 0) + 1;
            }
        }

        return $summary;
    }

    private function buildRouteSummary(): array
    {
        $summary = [
            'total'     => count($this->routes),
            'web'       => 0,
            'api'       => 0,
            'by_method' => [],
        ];

        foreach ($this->routes as $route) {
            $middlewares = $route['middleware'] ?? [];

            if (in_array('web', $middlewares)) {
                $summary['web']++;
            }

            if (in_array('api', $middlewares)) {
                $summary['api']++;
            }

            foreach ($route['methods'] ?? [] as $method) {
                if ($method === 'HEAD') {
                    continue;
                }
                $key = strtolower($method);
                $summary['by_method'][$key] = ($summary['by_method'][$key] ?? 0) + 1;
            }
        }

        return $summary;
    }

    public function toJson(): string
    {
        return json_encode($this->getReport(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
