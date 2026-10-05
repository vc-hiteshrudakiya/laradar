<?php

namespace Vcian\Laradar\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class ControllerAnalyzer
{
    public function __construct(private string $path) {}

    public function analyze(): array
    {
        if (!is_dir($this->path)) {
            return ['items' => [], 'errors' => []];
        }

        $items  = [];
        $errors = [];

        foreach (File::allFiles($this->path) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            try {
                $items[] = $this->processFile($file);
            } catch (\Throwable $e) {
                $errors[] = [
                    'file'    => $this->relativePath($file->getRealPath()),
                    'message' => $e->getMessage(),
                ];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    private function processFile(SplFileInfo $file): array
    {
        $content   = file_get_contents($file->getRealPath());
        $name      = $file->getFilenameWithoutExtension();
        $namespace = $this->detectNamespace($content);
        $methods   = $this->detectMethods($content);

        return [
            'name'           => $name,
            'path'           => $this->relativePath($file->getRealPath()),
            'namespace'      => $namespace,
            'full_class'     => $namespace ? $namespace . '\\' . $name : $name,
            'methods'        => $methods,
            'method_count'   => count($methods),
            'dependencies'   => $this->detectDependencies($content),
            'middleware'     => $this->detectMiddleware($content),
            'method_details' => $this->detectMethodDetails($content),
            'is_resource'    => $this->isResourceController($methods),
        ];
    }

    private function relativePath(string $absolutePath): string
    {
        return ltrim(str_replace(base_path(), '', $absolutePath), DIRECTORY_SEPARATOR);
    }

    private function detectNamespace(string $content): string
    {
        preg_match('/^namespace\s+([^;]+);/m', $content, $match);
        return isset($match[1]) ? trim($match[1]) : '';
    }

    private function detectMethods(string $content): array
    {
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $matches);

        return array_values(
            array_filter($matches[1], fn($method) => !str_starts_with($method, '__'))
        );
    }

    private function detectDependencies(string $content): array
    {
        if (!preg_match('/public\s+function\s+__construct\s*\(([^)]+)\)/s', $content, $m)) {
            return [];
        }
        // Handle union types (Foo|Bar $var), nullable (?Foo $var), intersection (Foo&Bar $var)
        preg_match_all('/\??([A-Z][\w\\\\]+(?:[|&]\??[A-Z][\w\\\\]+)*)\s+\$(\w+)/', $m[1], $hits);
        $deps = [];
        foreach ($hits[1] as $i => $typeExpr) {
            // For union/intersection types, use the first concrete type
            $primary = preg_split('/[|&]/', $typeExpr)[0];
            $short   = class_basename(str_replace('\\', '/', $primary));
            if (in_array($short, ['Request', 'Response', 'Application', 'Container'])) continue;
            $deps[] = ['type' => $short, 'var' => $hits[2][$i]];
        }
        return $deps;
    }

    private function detectMiddleware(string $content): array
    {
        $mw = [];

        // PHP 8 attribute: #[Middleware('auth')]
        preg_match_all('/#\[Middleware\([\'"]([^\'"]+)[\'"]\)\]/', $content, $m);
        array_push($mw, ...($m[1] ?? []));

        // $this->middleware('auth') or $this->middleware(['auth', 'verified'])
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

    private function detectMethodDetails(string $content): array
    {
        // Split file into individual method blocks to scope dispatch detection per-method
        preg_match_all(
            '/public\s+function\s+(\w+)\s*\(([^)]*)\)\s*(?::\s*[\w\\\\?|]+)?\s*\{/s',
            $content,
            $signatures,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        $details = [];

        foreach ($signatures as $i => $sig) {
            $name = $sig[1][0];
            if (str_starts_with($name, '__')) continue;

            // Extract method body (from opening brace to matching closing brace)
            $bodyStart = $sig[0][1] + strlen($sig[0][0]) - 1; // position of '{'
            $body      = $this->extractMethodBody($content, $bodyStart);

            // Parameters
            $params      = [];
            $formRequest = null;
            $sigStr      = trim($sig[2][0]);

            if ($sigStr !== '') {
                foreach (preg_split('/,(?![^<>]*>)/', $sigStr) as $param) {
                    $param = trim($param);
                    if (preg_match('/\??([A-Z][\w\\\\]+(?:[|&]\??[A-Z][\w\\\\]+)*)\s+\$(\w+)/', $param, $p)) {
                        $primary = class_basename(str_replace('\\', '/', preg_split('/[|&]/', $p[1])[0]));
                        $params[] = ['type' => $primary, 'var' => $p[2]];
                        // Detect FormRequest — type ending in Request (but not the base Request class)
                        if ($primary !== 'Request' && str_ends_with($primary, 'Request')) {
                            $formRequest = $primary;
                        }
                    } elseif (preg_match('/\$(\w+)/', $param, $p)) {
                        $params[] = ['type' => null, 'var' => $p[1]];
                    }
                }
            }

            // Events dispatched inside this method body
            $eventsDispatched = [];
            if ($body !== '') {
                preg_match_all('/\bevent\s*\(\s*new\s+([A-Z][A-Za-z0-9]+)\s*[,()\]]/', $body, $em);
                array_push($eventsDispatched, ...($em[1] ?? []));
                preg_match_all('/Event::dispatch\s*\(\s*new\s+([A-Z][A-Za-z0-9]+)\s*[,()\]]/', $body, $em);
                array_push($eventsDispatched, ...($em[1] ?? []));
                preg_match_all('/\b([A-Z][A-Za-z0-9]+Event)::dispatch\s*\(/', $body, $em);
                array_push($eventsDispatched, ...($em[1] ?? []));
                $eventsDispatched = array_values(array_unique($eventsDispatched));
            }

            // Jobs dispatched inside this method body
            $jobsDispatched = [];
            if ($body !== '') {
                preg_match_all('/\b([A-Z][A-Za-z0-9]+(?:Job)?)::dispatch(?:If|Unless)?\s*\(/', $body, $jm);
                array_push($jobsDispatched, ...($jm[1] ?? []));
                preg_match_all('/\bdispatch\s*\(\s*new\s+([A-Z][A-Za-z0-9]+)\s*[,()\]]/', $body, $jm);
                array_push($jobsDispatched, ...($jm[1] ?? []));
                $ignore         = ['Event', 'Mail', 'Notification'];
                $jobsDispatched = array_values(array_unique(array_filter($jobsDispatched, fn($j) => !in_array($j, $ignore))));
            }

            $details[] = [
                'method'           => $name,
                'params'           => $params,
                'form_request'     => $formRequest,
                'events_dispatched'=> $eventsDispatched,
                'jobs_dispatched'  => $jobsDispatched,
            ];
        }

        return $details;
    }

    // Walk the content from an opening brace and return the method body text
    private function extractMethodBody(string $content, int $openPos): string
    {
        $depth  = 0;
        $len    = strlen($content);
        $start  = $openPos;
        $end    = $openPos;

        for ($i = $openPos; $i < $len; $i++) {
            if ($content[$i] === '{') {
                $depth++;
            } elseif ($content[$i] === '}') {
                $depth--;
                if ($depth === 0) {
                    $end = $i;
                    break;
                }
            }
        }

        return $end > $start ? substr($content, $start + 1, $end - $start - 1) : '';
    }

    private function isResourceController(array $methods): bool
    {
        $resource = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
        $found    = array_intersect($methods, $resource);
        return count($found) >= 4;
    }
}
