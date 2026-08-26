<?php

namespace Vcian\Laradar\Analyzers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DeadCodeAnalyzer
{
    /** PHP files in app/ — lazy, used for class-reference checks. */
    private ?array $appFiles = null;

    /** PHP files in app/ + routes/ + tests/ + database/ — lazy, for broad scans. */
    private ?array $allProjectFiles = null;

    /** Patterns that identify a debug dump call. */
    private const DEBUG_PATTERNS = [
        'dd'       => '/\bdd\s*\(/',
        'dump'     => '/\bdump\s*\(/',
        'ddd'      => '/\bddd\s*\(/',
        'var_dump' => '/\bvar_dump\s*\(/',
        'print_r'  => '/\bprint_r\s*\(/',
        'ray'      => '/\bray\s*\(/',
    ];

    /**
     * Patterns that indicate a comment line contains PHP code rather than a plain
     * English sentence. A match on any of these = "commented-out code".
     */
    private const COMMENTED_CODE_PATTERNS = [
        '/\$\w+/',           // PHP variable  $foo
        '/->[\w]+\s*[\(\[]/', // method/prop  ->bar(  ->bar[
        '/::\w+/',           // static ref   Foo::bar
        '/\breturn\b/',      // return statement
        '/\bforeach\s*\(/',  // foreach loop
        '/\bif\s*\(/',       // if block
        '/\becho\b/',        // echo
        '/;\s*$/',           // statement terminator
        '/\bnew\s+[A-Z]/',   // new Class()
        '/\bthrow\b/',       // throw
    ];

    public function __construct(
        private string $appPath,
        private array  $controllers = [],
        private array  $models      = [],
        private array  $jobs        = [],
        private array  $events      = [],
        private array  $services    = [],
        private array  $routes      = [],
    ) {}

    public function analyze(): array
    {
        if (!is_dir($this->appPath)) {
            return ['items' => [], 'summary' => $this->emptySummary(), 'errors' => []];
        }

        $items  = [];
        $errors = [];

        try { array_push($items, ...$this->findDebugStatements());  } catch (\Throwable $e) { $errors[] = $e->getMessage(); }
        try { array_push($items, ...$this->findCommentedCode());    } catch (\Throwable $e) { $errors[] = $e->getMessage(); }
        try { array_push($items, ...$this->findOrphanMethods());    } catch (\Throwable $e) { $errors[] = $e->getMessage(); }
        try { array_push($items, ...$this->findUnusedModels());     } catch (\Throwable $e) { $errors[] = $e->getMessage(); }
        try { array_push($items, ...$this->findUndispatchedJobs()); } catch (\Throwable $e) { $errors[] = $e->getMessage(); }
        try { array_push($items, ...$this->findUnfiredEvents());    } catch (\Throwable $e) { $errors[] = $e->getMessage(); }
        try { array_push($items, ...$this->findUnusedServices());   } catch (\Throwable $e) { $errors[] = $e->getMessage(); }

        $summary = [
            'total'              => count($items),
            'high'               => count(array_filter($items, fn($i) => $i['severity'] === 'high')),
            'medium'             => count(array_filter($items, fn($i) => $i['severity'] === 'medium')),
            'low'                => count(array_filter($items, fn($i) => $i['severity'] === 'low')),
            'debug_statements'   => count(array_filter($items, fn($i) => $i['type'] === 'debug_statement')),
            'commented_code'     => count(array_filter($items, fn($i) => $i['type'] === 'commented_code')),
            'orphan_methods'     => count(array_filter($items, fn($i) => $i['type'] === 'orphan_method')),
            'unused_models'      => count(array_filter($items, fn($i) => $i['type'] === 'unused_model')),
            'undispatched_jobs'  => count(array_filter($items, fn($i) => $i['type'] === 'undispatched_job')),
            'unfired_events'     => count(array_filter($items, fn($i) => $i['type'] === 'unfired_event')),
            'unused_services'    => count(array_filter($items, fn($i) => $i['type'] === 'unused_service')),
        ];

        return compact('items', 'summary', 'errors');
    }

    // ── Detection methods ─────────────────────────────────────────────────────

    /**
     * Find dd(), dump(), var_dump() etc. — one item per occurrence with exact line number.
     * Also catches commented-out debug calls like: // dd($user);
     */
    private function findDebugStatements(): array
    {
        $found = [];

        foreach ($this->getAllProjectFiles() as $filePath) {
            $lines = @file($filePath, FILE_IGNORE_NEW_LINES);
            if (!$lines) continue;

            foreach ($lines as $idx => $line) {
                $lineNum = $idx + 1;

                $hits = [];
                foreach (self::DEBUG_PATTERNS as $fn => $pattern) {
                    if (preg_match($pattern, $line)) {
                        $hits[] = $fn . '()';
                    }
                }
                if (empty($hits)) continue;

                $rel = $this->relativePath($filePath);
                $found[] = [
                    'type'     => 'debug_statement',
                    'name'     => implode(' + ', $hits),
                    'method'   => null,
                    'path'     => $rel,
                    'line'     => $lineNum,
                    'severity' => 'high',
                    'snippet'  => trim($line),
                    'detail'   => 'Debug call left in code: ' . implode(', ', $hits),
                ];
            }
        }

        return $found;
    }

    /**
     * Detect blocks of commented-out PHP code in app/ files.
     * A line qualifies if it starts with // and its comment body matches PHP code patterns.
     * Groups consecutive qualifying lines into one finding to reduce noise.
     */
    private function findCommentedCode(): array
    {
        $found = [];

        foreach ($this->getAppFiles() as $filePath) {
            $lines = @file($filePath, FILE_IGNORE_NEW_LINES);
            if (!$lines) continue;

            $block      = [];  // line numbers in current streak
            $blockLines = [];  // raw text of those lines

            $flush = function () use (&$block, &$blockLines, $filePath, &$found) {
                // Only report blocks of 2+ lines; single lines create too much noise
                if (count($block) >= 2) {
                    $rel = $this->relativePath($filePath);
                    $found[] = [
                        'type'     => 'commented_code',
                        'name'     => basename($filePath) . ' lines ' . $block[0] . '–' . end($block),
                        'method'   => null,
                        'path'     => $rel,
                        'line'     => $block[0],
                        'severity' => 'low',
                        'snippet'  => implode("\n", array_slice($blockLines, 0, 3)) . (count($blockLines) > 3 ? "\n…" : ''),
                        'detail'   => count($block) . ' consecutive commented-out code lines starting at line ' . $block[0],
                    ];
                }
                $block      = [];
                $blockLines = [];
            };

            foreach ($lines as $idx => $line) {
                $lineNum = $idx + 1;
                $trimmed = ltrim($line);

                if (str_starts_with($trimmed, '//')) {
                    // The comment content (strip the // prefix)
                    $body = ltrim(substr($trimmed, 2));
                    if ($this->looksLikeCode($body)) {
                        $block[]      = $lineNum;
                        $blockLines[] = trim($line);
                        continue;
                    }
                }

                $flush();
            }
            $flush();
        }

        return $found;
    }

    private function looksLikeCode(string $commentBody): bool
    {
        foreach (self::COMMENTED_CODE_PATTERNS as $pattern) {
            if (preg_match($pattern, $commentBody)) {
                return true;
            }
        }
        return false;
    }

    private function findOrphanMethods(): array
    {
        $found = [];

        foreach ($this->controllers as $ctrl) {
            foreach ($ctrl['methods'] ?? [] as $method) {
                $methodName = is_array($method) ? ($method['name'] ?? $method) : $method;

                if (str_starts_with($methodName, '__')) continue;
                if (in_array($methodName, ['handle', 'boot', 'register', 'middleware', 'rules', 'authorize'])) continue;

                if ($this->isMethodRouted($ctrl['name'] ?? '', $methodName)) continue;

                if (!$this->isSymbolReferenced($methodName, $ctrl['path'] ?? null)) {
                    $found[] = [
                        'type'     => 'orphan_method',
                        'name'     => ($ctrl['name'] ?? '?') . '::' . $methodName,
                        'method'   => $methodName,
                        'path'     => $ctrl['path'] ?? '',
                        'line'     => null,
                        'severity' => 'medium',
                        'snippet'  => null,
                        'detail'   => 'Method not registered in any route or referenced elsewhere.',
                    ];
                }
            }
        }

        return $found;
    }

    private function findUnusedModels(): array
    {
        $found = [];

        foreach ($this->models as $model) {
            $className = $model['name'] ?? null;
            if (!$className) continue;

            if ($this->isRelatedModel($className)) continue;

            if (!$this->isClassReferenced($className, $model['path'] ?? null)) {
                $found[] = [
                    'type'     => 'unused_model',
                    'name'     => $className,
                    'method'   => null,
                    'path'     => $model['path'] ?? '',
                    'line'     => null,
                    'severity' => 'high',
                    'snippet'  => null,
                    'detail'   => 'Model class not referenced anywhere in the codebase.',
                ];
            }
        }

        return $found;
    }

    private function findUndispatchedJobs(): array
    {
        $found = [];

        foreach ($this->jobs as $job) {
            $className = $job['name'] ?? null;
            if (!$className) continue;

            if (!$this->isClassReferenced($className, $job['path'] ?? null)) {
                $found[] = [
                    'type'     => 'undispatched_job',
                    'name'     => $className,
                    'method'   => null,
                    'path'     => $job['path'] ?? '',
                    'line'     => null,
                    'severity' => 'medium',
                    'snippet'  => null,
                    'detail'   => 'Job class never dispatched (no reference found).',
                ];
            }
        }

        return $found;
    }

    private function findUnfiredEvents(): array
    {
        $found = [];

        foreach ($this->events as $event) {
            $className = $event['name'] ?? null;
            if (!$className) continue;

            if (!$this->isClassReferenced($className, $event['path'] ?? null)) {
                $found[] = [
                    'type'     => 'unfired_event',
                    'name'     => $className,
                    'method'   => null,
                    'path'     => $event['path'] ?? '',
                    'line'     => null,
                    'severity' => 'low',
                    'snippet'  => null,
                    'detail'   => 'Event class never fired (no reference found).',
                ];
            }
        }

        return $found;
    }

    private function findUnusedServices(): array
    {
        $found = [];

        foreach ($this->services as $service) {
            $className = $service['name'] ?? null;
            if (!$className) continue;

            if (!$this->isClassReferenced($className, $service['path'] ?? null)) {
                $found[] = [
                    'type'     => 'unused_service',
                    'name'     => $className,
                    'method'   => null,
                    'path'     => $service['path'] ?? '',
                    'line'     => null,
                    'severity' => 'medium',
                    'snippet'  => null,
                    'detail'   => 'Service class not injected or referenced anywhere.',
                ];
            }
        }

        return $found;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * RouteAnalyzer stores: ['controller' => ['class' => '...Controller', 'method' => 'index']]
     * NOT a flat 'action' string — read the correct nested keys.
     */
    private function isMethodRouted(string $controllerName, string $methodName): bool
    {
        foreach ($this->routes as $route) {
            $ctrlData = $route['controller'] ?? [];

            if (is_array($ctrlData)) {
                $routeMethod = $ctrlData['method'] ?? '';
                $routeClass  = $ctrlData['class']  ?? '';
            } else {
                $routeMethod = $route['action'] ?? '';
                $routeClass  = (string) $ctrlData;
            }

            if ($routeMethod === $methodName && str_contains($routeClass, $controllerName)) {
                return true;
            }
        }
        return false;
    }

    private function isRelatedModel(string $className): bool
    {
        foreach ($this->models as $model) {
            foreach ($model['relationships'] ?? [] as $rel) {
                if (($rel['related'] ?? '') === $className) return true;
            }
        }
        return false;
    }

    private function isClassReferenced(string $className, ?string $excludePath): bool
    {
        $pattern = '/\b' . preg_quote($className, '/') . '\b/';
        return $this->grepFiles($this->getAllProjectFiles(), $pattern, $excludePath);
    }

    private function isSymbolReferenced(string $symbol, ?string $excludePath): bool
    {
        $pattern = '/\b' . preg_quote($symbol, '/') . '\b/';
        return $this->grepFiles($this->getAllProjectFiles(), $pattern, $excludePath);
    }

    private function grepFiles(array $files, string $pattern, ?string $excludePath): bool
    {
        $excludeSuffix = $excludePath ? ltrim($excludePath, DIRECTORY_SEPARATOR) : null;

        foreach ($files as $filePath) {
            if ($excludeSuffix && str_ends_with($filePath, $excludeSuffix)) {
                continue;
            }
            $content = @file_get_contents($filePath);
            if ($content && preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    private function getAllProjectFiles(): array
    {
        if ($this->allProjectFiles !== null) {
            return $this->allProjectFiles;
        }

        $base = dirname($this->appPath);

        $dirs = array_filter([
            $this->appPath,
            $base . DIRECTORY_SEPARATOR . 'routes',
            $base . DIRECTORY_SEPARATOR . 'tests',
            $base . DIRECTORY_SEPARATOR . 'database',
        ], 'is_dir');

        $this->allProjectFiles = [];
        foreach ($dirs as $dir) {
            array_push($this->allProjectFiles, ...$this->collectPhpFiles($dir));
        }

        return $this->allProjectFiles;
    }

    private function getAppFiles(): array
    {
        if ($this->appFiles === null) {
            $this->appFiles = $this->collectPhpFiles($this->appPath);
        }
        return $this->appFiles;
    }

    private function collectPhpFiles(string $dir): array
    {
        $files = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }

    private function relativePath(string $absolutePath): string
    {
        $base = dirname($this->appPath);
        return ltrim(str_replace($base, '', $absolutePath), DIRECTORY_SEPARATOR);
    }

    private function emptySummary(): array
    {
        return [
            'total'              => 0,
            'high'               => 0,
            'medium'             => 0,
            'low'                => 0,
            'debug_statements'   => 0,
            'commented_code'     => 0,
            'orphan_methods'     => 0,
            'unused_models'      => 0,
            'undispatched_jobs'  => 0,
            'unfired_events'     => 0,
            'unused_services'    => 0,
        ];
    }
}
