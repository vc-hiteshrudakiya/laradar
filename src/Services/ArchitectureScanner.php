<?php

namespace Vcian\Laradar\Services;

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

        // Core analyzers
        $controllerItems = [];
        $routeItems      = [];

        foreach (['models', 'controllers', 'routes'] as $type) {
            if (!isset($this->analyzers[$type])) continue;

            $result = $this->analyzers[$type]->analyze();

            $addItem = match ($type) {
                'models'      => fn($item) => $report->addModel($item),
                'controllers' => fn($item) => $report->addController($item),
                'routes'      => fn($item) => $report->addRoute($item),
            };

            foreach ($result['items']  as $item)  {
                $addItem($item);
                if ($type === 'controllers') $controllerItems[] = $item;
                if ($type === 'routes')      $routeItems[]      = $item;
            }
            foreach ($result['errors'] as $error) { $report->addError($error); }
        }

        foreach ($this->resolveCustomHandlers($routeItems, $controllerItems) as $handler) {
            $report->addController($handler);
        }

        // Extended analyzers
        $extendedMap = [
            'jobs'         => fn($item) => $report->addJob($item),
            'events'       => fn($item) => $report->addEvent($item),
            'services'     => fn($item) => $report->addService($item),
            'repositories' => fn($item) => $report->addRepository($item),
            'observers'    => fn($item) => $report->addObserver($item),
            'policies'     => fn($item) => $report->addPolicy($item),
            'modules'      => fn($item) => $report->addModule($item),
            'packages'     => fn($item) => $report->addPackage($item),
            'migrations'   => fn($item) => $report->addMigration($item),
        ];

        foreach ($extendedMap as $type => $addFn) {
            if (!isset($this->analyzers[$type])) continue;
            $result = $this->analyzers[$type]->analyze();
            foreach ($result['items']  as $item)  { $addFn($item); }
            foreach ($result['errors'] as $error) { $report->addError($error); }
        }

        $report->setPerformance(
            (microtime(true) - $startTime) * 1000,
            (memory_get_usage(true) - $startMemory) / 1024 / 1024,
        );

        if (isset($this->analyzers['dependencies'])) {
            $result = $this->analyzers['dependencies']->analyze();
            $report->setDependencies($result);

            // Apply cross-reference data to enrich services, events, and jobs
            $crossRefs = $result['cross_refs'] ?? [];
            $report->enrichServices($crossRefs['service_callers']   ?? []);
            $report->enrichEvents($crossRefs['event_dispatchers']   ?? []);
            $report->enrichJobs($crossRefs['job_dispatchers']       ?? []);
        }

        $report->setScore((new ArchitectureScorer())->score($report->getReport()));

        return $report;
    }

    private function resolveCustomHandlers(array $routes, array $controllers): array
    {
        $scanned = array_flip(array_map('strtolower', array_filter(array_column($controllers, 'full_class'))));

        $handlerClasses = [];
        foreach ($routes as $route) {
            $class = $route['controller']['class'] ?? null;
            if (!$class || $class === 'Closure') continue;
            if (isset($scanned[strtolower($class)])) continue;
            $handlerClasses[$class] = true;
        }

        if (empty($handlerClasses)) {
            return [];
        }

        $loader = require base_path('vendor/autoload.php');
        $items  = [];

        foreach (array_keys($handlerClasses) as $class) {
            try {
                $file = $loader->findFile($class);
                if (!$file || !file_exists($file)) continue;

                $content = file_get_contents($file);
                $name    = basename($file, '.php');
                $methods = $this->chParsePublicMethods($content);

                $items[] = [
                    'name'              => $name,
                    'path'              => ltrim(str_replace(base_path(), '', realpath($file)), DIRECTORY_SEPARATOR),
                    'namespace'         => $this->chParseNamespace($content),
                    'full_class'        => $class,
                    'methods'           => $methods,
                    'method_count'      => count($methods),
                    'dependencies'      => $this->chParseDependencies($content),
                    'middleware'        => $this->chParseMiddleware($content),
                    'method_details'    => $this->chParseMethodDetails($content),
                    'is_resource'       => false,
                    'is_custom_handler' => true,
                    'handler_type'      => $this->chDetectHandlerType($content, $class, $methods),
                ];
            } catch (\Throwable) {
                // Skip unresolvable handlers silently
            }
        }

        return $items;
    }

    private function chParseNamespace(string $content): string
    {
        preg_match('/^namespace\s+([^;]+);/m', $content, $m);
        return isset($m[1]) ? trim($m[1]) : '';
    }

    private function chParsePublicMethods(string $content): array
    {
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $m);
        return array_values(array_filter($m[1] ?? [], fn($method) => !str_starts_with($method, '__')));
    }

    private function chParseDependencies(string $content): array
    {
        if (!preg_match('/public\s+function\s+__construct\s*\(([^)]+)\)/s', $content, $m)) {
            return [];
        }
        preg_match_all('/\??([A-Z][\w\\\\]+(?:[|&]\??[A-Z][\w\\\\]+)*)\s+\$(\w+)/', $m[1], $hits);
        $deps = [];
        foreach ($hits[1] as $i => $typeExpr) {
            $primary = preg_split('/[|&]/', $typeExpr)[0];
            $short   = class_basename(str_replace('\\', '/', $primary));
            if (in_array($short, ['Request', 'Response', 'Application', 'Container'])) continue;
            $deps[] = ['type' => $short, 'var' => $hits[2][$i]];
        }
        return $deps;
    }

    private function chParseMiddleware(string $content): array
    {
        $mw = [];
        preg_match_all('/#\[Middleware\([\'"]([^\'"]+)[\'"]\)\]/', $content, $m);
        array_push($mw, ...($m[1] ?? []));
        preg_match_all('/\$this->middleware\(\s*[\'"]([\w:,\s|]+)[\'"]/m', $content, $m);
        foreach ($m[1] ?? [] as $item) {
            array_push($mw, ...array_map('trim', explode(',', $item)));
        }
        preg_match_all('/\$this->middleware\(\s*\[([^\]]+)\]/s', $content, $m);
        foreach ($m[1] ?? [] as $block) {
            preg_match_all('/[\'"]([^\'"]+)[\'"]/', $block, $inner);
            array_push($mw, ...($inner[1] ?? []));
        }
        return array_values(array_unique(array_filter($mw)));
    }

    private function chParseMethodDetails(string $content): array
    {
        preg_match_all('/public\s+function\s+(\w+)\s*\(([^)]*)\)/s', $content, $matches, PREG_SET_ORDER);
        $details = [];
        foreach ($matches as $match) {
            $name = $match[1];
            if (str_starts_with($name, '__')) continue;
            $params = [];
            $sig    = trim($match[2]);
            if ($sig !== '') {
                foreach (preg_split('/,(?![^<>]*>)/', $sig) as $param) {
                    $param = trim($param);
                    if (preg_match('/\??([A-Z][\w\\\\]+(?:[|&]\??[A-Z][\w\\\\]+)*)\s+\$(\w+)/', $param, $p)) {
                        $primary  = preg_split('/[|&]/', $p[1])[0];
                        $params[] = ['type' => class_basename(str_replace('\\', '/', $primary)), 'var' => $p[2]];
                    } elseif (preg_match('/\$(\w+)/', $param, $p)) {
                        $params[] = ['type' => null, 'var' => $p[1]];
                    }
                }
            }
            $details[] = ['method' => $name, 'params' => $params];
        }
        return $details;
    }

    private function chDetectHandlerType(string $content, string $class, array $methods): string
    {
        if (str_contains($class, 'Filament') || preg_match('/extends\s+[\w\\\\]*Page\b/', $content)) {
            return 'Filament Page';
        }
        if (str_contains($class, 'Livewire') || preg_match('/extends\s+[\w\\\\]*Component\b/', $content)) {
            return 'Livewire Component';
        }
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $m);
        if (in_array('__invoke', $m[1] ?? []) && count($methods) === 0) {
            return 'Invokable Class';
        }
        return 'Custom Handler';
    }
}
