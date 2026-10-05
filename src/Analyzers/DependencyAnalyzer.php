<?php

namespace Vcian\Laradar\Analyzers;

class DependencyAnalyzer
{
    public function __construct(private string $appPath) {}

    public function analyze(): array
    {
        if (!is_dir($this->appPath)) {
            return ['nodes' => [], 'edges' => [], 'errors' => []];
        }

        $nodes  = [];
        $errors = [];

        // Pass 1 — register every app class, keyed by FQN to avoid short-name collisions
        foreach ($this->phpFiles() as $file) {
            try {
                $content   = file_get_contents($file);
                $name      = $this->className($content);
                if (!$name) continue;

                $namespace = $this->extractNamespace($content);
                $fqn       = $namespace ? "$namespace\\$name" : $name;

                $layer = $this->detectLayer($name, $file, $content);
                if ($layer === 'other') continue;

                $nodes[$fqn] = [
                    'name'               => $name,
                    'fqn'                => $fqn,
                    'layer'              => $layer,
                    'file'               => ltrim(str_replace(base_path(), '', $file), DIRECTORY_SEPARATOR),
                    '_ctorDeps'          => $this->extractConstructorDeps($content),
                    '_handleDeps'        => $this->extractHandleDeps($content),
                    '_usedModels'        => $this->extractUsedClasses($content),
                    '_methodDeps'        => $this->extractMethodBodyDeps($content),
                    '_implements'        => $this->extractImplemented($content),
                    '_useTraits'         => $this->extractUsedTraits($content),
                    '_dispatchedEvents'  => $this->extractDispatchedEvents($content),
                    '_dispatchedJobs'    => $this->extractDispatchedJobs($content),
                ];
            } catch (\Throwable $e) {
                $errors[] = ['file' => $file, 'message' => $e->getMessage()];
            }
        }

        // Add virtual Database node if any models were found
        $hasModels = !empty(array_filter($nodes, fn($n) => $n['layer'] === 'model'));
        if ($hasModels) {
            $nodes['Database'] = [
                'name'               => 'Database',
                'fqn'                => 'Database',
                'layer'              => 'database',
                'file'               => '',
                '_ctorDeps'          => [],
                '_handleDeps'        => [],
                '_usedModels'        => [],
                '_methodDeps'        => [],
                '_implements'        => [],
                '_useTraits'         => [],
                '_dispatchedEvents'  => [],
                '_dispatchedJobs'    => [],
            ];
        }

        // Build short-name → [fqn, ...] index to resolve deps without collisions
        $shortNames = [];
        foreach ($nodes as $fqn => $node) {
            $shortNames[$node['name']][] = $fqn;
        }

        // Pass 2 — resolve edges
        $edgeSet = [];

        foreach ($nodes as $fqn => $node) {
            if ($fqn === 'Database') continue;
            $name = $node['name'];

            // Constructor injection → injects edges (Controller→Service, Service→Repo, Job→Service …)
            foreach ($node['_ctorDeps'] as $dep) {
                $short = class_basename($dep);
                foreach ($shortNames[$short] ?? [] as $targetFqn) {
                    if ($targetFqn !== $fqn) {
                        $this->addEdge($edgeSet, $name, $nodes[$targetFqn]['name'], 'injects');
                    }
                }
            }

            // Listener handle(SomeEvent $e) → Event → Listener (dispatch direction)
            if ($node['layer'] === 'listener') {
                foreach ($node['_handleDeps'] as $dep) {
                    $short = class_basename($dep);
                    foreach ($shortNames[$short] ?? [] as $targetFqn) {
                        if ($nodes[$targetFqn]['layer'] === 'event') {
                            $this->addEdge($edgeSet, $nodes[$targetFqn]['name'], $name, 'triggers');
                        }
                    }
                }
            }

            // Controller / Repository / Service → Model (via use imports + static calls + new)
            if (in_array($node['layer'], ['controller', 'repository', 'service'])) {
                foreach ($node['_usedModels'] as $used) {
                    foreach ($shortNames[$used] ?? [] as $targetFqn) {
                        if ($nodes[$targetFqn]['layer'] === 'model') {
                            $this->addEdge($edgeSet, $name, $nodes[$targetFqn]['name'], 'uses');
                        }
                    }
                }
            }

            // Controller / Service / Job → Service / Repository via method-body type hints
            if (in_array($node['layer'], ['controller', 'service', 'job'])) {
                foreach ($node['_methodDeps'] as $dep) {
                    $short = class_basename($dep);
                    foreach ($shortNames[$short] ?? [] as $targetFqn) {
                        if ($targetFqn !== $fqn) {
                            $this->addEdge($edgeSet, $name, $nodes[$targetFqn]['name'], 'uses');
                        }
                    }
                }
            }

            // Model → Database (all models persist to DB)
            if ($node['layer'] === 'model' && $hasModels) {
                $this->addEdge($edgeSet, $name, 'Database', 'persists');
            }

            // Class implements Interface
            foreach ($node['_implements'] as $iface) {
                foreach ($shortNames[$iface] ?? [] as $targetFqn) {
                    if ($nodes[$targetFqn]['layer'] === 'interface') {
                        $this->addEdge($edgeSet, $name, $nodes[$targetFqn]['name'], 'implements');
                    }
                }
            }

            // Class uses Trait
            foreach ($node['_useTraits'] as $trait) {
                foreach ($shortNames[$trait] ?? [] as $targetFqn) {
                    if ($nodes[$targetFqn]['layer'] === 'trait') {
                        $this->addEdge($edgeSet, $name, $nodes[$targetFqn]['name'], 'uses_trait');
                    }
                }
            }
        }

        // Pass 3 — build cross-reference maps using internal fields before they are stripped
        $crossRefs = $this->buildCrossRefs($nodes, $shortNames);

        // Strip internal fields before returning
        foreach ($nodes as $fqn => $_) {
            unset(
                $nodes[$fqn]['_ctorDeps'],
                $nodes[$fqn]['_handleDeps'],
                $nodes[$fqn]['_usedModels'],
                $nodes[$fqn]['_methodDeps'],
                $nodes[$fqn]['_implements'],
                $nodes[$fqn]['_useTraits'],
                $nodes[$fqn]['_dispatchedEvents'],
                $nodes[$fqn]['_dispatchedJobs'],
            );
        }

        return [
            'nodes'      => array_values($nodes),
            'edges'      => array_values($edgeSet),
            'errors'     => $errors,
            'cross_refs' => $crossRefs,
        ];
    }

    private function addEdge(array &$set, string $from, string $to, string $type): void
    {
        $key = $from . '|' . $to . '|' . $type;
        if (!isset($set[$key])) {
            $set[$key] = ['from' => $from, 'to' => $to, 'type' => $type];
        }
    }

    private function phpFiles(): \Generator
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->appPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getExtension() === 'php') {
                yield $file->getPathname();
            }
        }
    }

    private function extractNamespace(string $content): string
    {
        return preg_match('/^namespace\s+([\w\\\\]+)\s*;/m', $content, $m) ? $m[1] : '';
    }

    private function className(string $content): ?string
    {
        return preg_match('/\b(?:class|interface|trait)\s+(\w+)/', $content, $m) ? $m[1] : null;
    }

    private function detectLayer(string $name, string $file, string $content): string
    {
        $sep = DIRECTORY_SEPARATOR;

        if (preg_match('/\binterface\s+' . preg_quote($name, '/') . '\b/', $content)) return 'interface';
        if (preg_match('/\btrait\s+' . preg_quote($name, '/') . '\b/', $content))     return 'trait';

        if (str_ends_with($name, 'Service'))    return 'service';
        if (str_ends_with($name, 'Repository')) return 'repository';

        if (str_ends_with($name, 'Listener'))                                            return 'listener';
        if (str_ends_with($name, 'Job') || str_contains($file, "{$sep}Jobs{$sep}"))      return 'job';
        if (str_ends_with($name, 'Event') || str_contains($file, "{$sep}Events{$sep}"))  return 'event';

        if (str_contains($file, "{$sep}Models{$sep}") ||
            preg_match('/extends\s+(?:Eloquent\\\\)?Model\b/', $content)) {
            return 'model';
        }

        if (str_ends_with($name, 'Controller')) return 'controller';

        if (str_contains($file, "{$sep}Filament{$sep}") ||
            str_contains($file, "{$sep}Livewire{$sep}") ||
            str_ends_with($name, 'Page')      ||
            str_ends_with($name, 'Resource')  ||
            str_ends_with($name, 'Widget')    ||
            str_ends_with($name, 'Component') ||
            preg_match('/extends\s+(?:Page|Resource|Widget|Component|ListRecords|CreateRecord|EditRecord|ViewRecord|ManageRecords)\b/', $content)) {
            return 'controller';
        }

        return 'other';
    }

    private function extractConstructorDeps(string $content): array
    {
        if (!preg_match('/public\s+function\s+__construct\s*\(([^)]+)\)/s', $content, $m)) {
            return [];
        }
        preg_match_all('/\b([A-Z][\w\\\\]+)\s+\$/', $m[1], $hits);
        return array_unique($hits[1] ?? []);
    }

    private function extractHandleDeps(string $content): array
    {
        if (!preg_match('/public\s+function\s+handle\s*\(([^)]+)\)/s', $content, $m)) {
            return [];
        }
        preg_match_all('/\b([A-Z][\w\\\\]+)\s+\$/', $m[1], $hits);
        return array_unique($hits[1] ?? []);
    }

    private function extractUsedClasses(string $content): array
    {
        $found = [];

        preg_match_all('/^use\s+(?:[A-Za-z0-9\\\\]+\\\\)?([A-Z][A-Za-z0-9]+)(?:\s+as\s+\w+)?\s*;/m', $content, $m);
        array_push($found, ...($m[1] ?? []));

        preg_match_all(
            '/\b([A-Z][A-Za-z0-9]+)::(?:find|findOrFail|all|where|with|create|firstOrCreate|updateOrCreate|first|firstOrFail|get|select|orderBy|paginate|count|sum|exists|doesntExist|latest|oldest)\b/',
            $content, $m
        );
        array_push($found, ...($m[1] ?? []));

        preg_match_all('/\bnew\s+([A-Z][A-Za-z0-9]+)\s*\(/', $content, $m);
        array_push($found, ...($m[1] ?? []));

        $ignore = ['DB','Schema','Carbon','Str','Arr','Log','Cache','Config','Request','Response',
                   'Auth','Gate','Hash','Storage','Mail','Event','Queue','Redirect','Route','Session','Validator'];

        return array_unique(array_filter($found, fn($c) => !in_array($c, $ignore)));
    }

    private function extractImplemented(string $content): array
    {
        // PHP syntax: `extends` always precedes `implements`, so `implements` is always followed by `{`
        if (!preg_match('/\bimplements\s+([\w,\s\\\\]+?)\s*\{/s', $content, $m)) {
            return [];
        }
        $parts = preg_split('/\s*,\s*/', trim($m[1]));
        return array_values(array_filter(array_map(
            fn($p) => class_basename(trim($p)),
            $parts
        )));
    }

    private function extractUsedTraits(string $content): array
    {
        $classPos = strpos($content, '{');
        if ($classPos === false) return [];
        $body = substr($content, $classPos);

        $traits = [];
        if (preg_match('/\buse\s+([\w,\s\\\\]+?)\s*;/s', $body, $m)) {
            foreach (preg_split('/\s*,\s*/', $m[1]) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $traits[] = class_basename($part);
                }
            }
        }
        return $traits;
    }

    private function buildCrossRefs(array $nodes, array $shortNames): array
    {
        $serviceCallers    = [];
        $eventDispatchers  = [];
        $jobDispatchers    = [];

        foreach ($nodes as $fqn => $node) {
            $callerName = $node['name'];

            // Service / Repository "Used By" — controllers that inject them via constructor
            if (in_array($node['layer'], ['controller'])) {
                foreach ($node['_ctorDeps'] as $dep) {
                    $short = class_basename($dep);
                    foreach ($shortNames[$short] ?? [] as $targetFqn) {
                        if (in_array($nodes[$targetFqn]['layer'] ?? '', ['service', 'repository'])) {
                            $serviceCallers[$short][] = $callerName;
                        }
                    }
                }
            }

            // Event "Dispatched By" — any class that calls event(new X) or X::dispatch()
            foreach ($node['_dispatchedEvents'] ?? [] as $eventShort) {
                $eventDispatchers[$eventShort][] = $callerName;
            }

            // Job "Dispatched By" — any class that calls X::dispatch() or dispatch(new X)
            foreach ($node['_dispatchedJobs'] ?? [] as $jobShort) {
                $jobDispatchers[$jobShort][] = $callerName;
            }
        }

        // Deduplicate
        foreach ($serviceCallers   as $k => $v) { $serviceCallers[$k]   = array_values(array_unique($v)); }
        foreach ($eventDispatchers as $k => $v) { $eventDispatchers[$k] = array_values(array_unique($v)); }
        foreach ($jobDispatchers   as $k => $v) { $jobDispatchers[$k]   = array_values(array_unique($v)); }

        return [
            'service_callers'   => $serviceCallers,
            'event_dispatchers' => $eventDispatchers,
            'job_dispatchers'   => $jobDispatchers,
        ];
    }

    private function extractDispatchedEvents(string $content): array
    {
        $events = [];

        // event(new SomeEvent(...))
        preg_match_all('/\bevent\s*\(\s*new\s+([A-Z][A-Za-z0-9]+)\s*[,()\]]/', $content, $m);
        array_push($events, ...($m[1] ?? []));

        // SomeEvent::dispatch(...)
        preg_match_all('/\b([A-Z][A-Za-z0-9]+Event)::dispatch\s*\(/', $content, $m);
        array_push($events, ...($m[1] ?? []));

        // Event::dispatch(new SomeEvent(...))
        preg_match_all('/Event::dispatch\s*\(\s*new\s+([A-Z][A-Za-z0-9]+)\s*[,()\]]/', $content, $m);
        array_push($events, ...($m[1] ?? []));

        return array_values(array_unique($events));
    }

    private function extractDispatchedJobs(string $content): array
    {
        $jobs = [];

        // SomeJob::dispatch(...) or SomeJob::dispatchIf(...) or SomeJob::dispatchUnless(...)
        preg_match_all('/\b([A-Z][A-Za-z0-9]+(?:Job)?)::dispatch(?:If|Unless)?\s*\(/', $content, $m);
        array_push($jobs, ...($m[1] ?? []));

        // dispatch(new SomeJob(...))
        preg_match_all('/\bdispatch\s*\(\s*new\s+([A-Z][A-Za-z0-9]+)\s*[,()\]]/', $content, $m);
        array_push($jobs, ...($m[1] ?? []));

        // Queue::push(new SomeJob(...))
        preg_match_all('/Queue::push\s*\(\s*new\s+([A-Z][A-Za-z0-9]+)\s*[,()\]]/', $content, $m);
        array_push($jobs, ...($m[1] ?? []));

        // Filter out known non-job classes
        $ignore = ['Event', 'Mail', 'Notification'];

        return array_values(array_unique(array_filter($jobs, fn($j) => !in_array($j, $ignore))));
    }

    private function extractMethodBodyDeps(string $content): array
    {
        $found = [];

        preg_match_all(
            '/public\s+function\s+(?!__construct)\w+\s*\([^)]*\b([A-Z][A-Za-z0-9]+)\s+\$/',
            $content, $m
        );
        array_push($found, ...($m[1] ?? []));

        preg_match_all('/(?:app|resolve|make)\(\s*([A-Z][A-Za-z0-9]+)::class/', $content, $m);
        array_push($found, ...($m[1] ?? []));

        $ignore = ['Request','Response','Model','Controller','FormRequest','Resource','Notification','Mailable','ShouldQueue'];

        return array_unique(array_filter($found, fn($c) => !in_array($c, $ignore)));
    }
}
