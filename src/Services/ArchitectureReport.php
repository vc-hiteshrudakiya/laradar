<?php

namespace Hitesh\LaravelArchitectureDiscovery\Services;

use Hitesh\LaravelArchitectureDiscovery\ArchitectureDiscovery;

class ArchitectureReport
{
    private array $models       = [];
    private array $controllers  = [];
    private array $routes       = [];
    private array $errors       = [];
    private array $performance  = [];
    private array $dependencies = ['nodes' => [], 'edges' => []];
    private array $score        = [];

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

    public function addModel(array $model): void       { $this->models[]      = $model; }
    public function addController(array $c): void      { $this->controllers[] = $c; }
    public function addRoute(array $route): void       { $this->routes[]      = $route; }
    public function addError(array $error): void       { $this->errors[]      = $error; }

    public function setDependencies(array $graph): void
    {
        $this->dependencies = [
            'nodes' => $graph['nodes'] ?? [],
            'edges' => $graph['edges'] ?? [],
        ];
    }

    public function setScore(array $score): void
    {
        $this->score = $score;
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
            'performance'  => $this->performance,
            'score'        => $this->score,
            'summary'      => [
                'models'               => count($this->models),
                'controllers'          => count($this->controllers),
                'routes'               => count($this->routes),
                'relationship_summary' => $this->buildRelationshipSummary(),
            ],
            'route_summary' => $this->buildRouteSummary(),
            'dependencies'  => $this->dependencies,
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
            'total'            => count($this->routes),
            'web'              => 0,
            'api'              => 0,
            'by_method'        => [],
            'middleware_usage' => [],
            'named_count'      => 0,
            'api_versions'     => [],
        ];

        foreach ($this->routes as $route) {
            $middlewares = $route['middleware'] ?? [];

            if (in_array('web', $middlewares)) $summary['web']++;
            if (in_array('api', $middlewares)) $summary['api']++;

            // Named routes
            if (!empty($route['name'])) {
                $summary['named_count']++;
            }

            // Middleware usage count
            foreach ($middlewares as $mw) {
                $key = strtolower($mw);
                $summary['middleware_usage'][$key] = ($summary['middleware_usage'][$key] ?? 0) + 1;
            }

            // By HTTP method
            foreach ($route['methods'] ?? [] as $method) {
                if ($method === 'HEAD') continue;
                $key = strtolower($method);
                $summary['by_method'][$key] = ($summary['by_method'][$key] ?? 0) + 1;
            }

            // API version detection: api/v1/... or api/v2/...
            if (preg_match('#^api/v(\d+)(?:/|$)#i', $route['uri'] ?? '', $vm)) {
                $ver = 'v' . $vm[1];
                $summary['api_versions'][$ver] = ($summary['api_versions'][$ver] ?? 0) + 1;
            }
        }

        // Sort middleware by usage descending
        arsort($summary['middleware_usage']);

        return $summary;
    }

    public function toJson(): string
    {
        return json_encode($this->getReport(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
