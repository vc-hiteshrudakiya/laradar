<?php

namespace Hitesh\LaravelArchitectureDiscovery\Analyzers;

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

        // Pass 1 — register every app class with its layer and raw deps
        foreach ($this->phpFiles() as $file) {
            try {
                $content = file_get_contents($file);
                $name    = $this->className($content);
                if (!$name) continue;

                $layer = $this->layer($name, $file);
                if ($layer === 'other') continue;

                $nodes[$name] = [
                    'name'     => $name,
                    'layer'    => $layer,
                    'file'     => ltrim(str_replace(base_path(), '', $file), DIRECTORY_SEPARATOR),
                    '_rawDeps' => $this->extractDeps($content),
                ];
            } catch (\Throwable $e) {
                $errors[] = ['file' => $file, 'message' => $e->getMessage()];
            }
        }

        // Pass 2 — resolve edges only between known nodes
        $edges = [];
        foreach ($nodes as $name => $node) {
            foreach ($node['_rawDeps'] as $dep) {
                $short = class_basename($dep);
                if (isset($nodes[$short]) && $short !== $name) {
                    $edge = ['from' => $name, 'to' => $short];
                    if (!in_array($edge, $edges)) {
                        $edges[] = $edge;
                    }
                }
            }
            unset($nodes[$name]['_rawDeps']);
        }

        return [
            'nodes'  => array_values($nodes),
            'edges'  => $edges,
            'errors' => $errors,
        ];
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

    private function className(string $content): ?string
    {
        return preg_match('/\bclass\s+(\w+)/', $content, $m) ? $m[1] : null;
    }

    private function layer(string $name, string $file): string
    {
        if (str_ends_with($name, 'Controller')) return 'controller';
        if (str_ends_with($name, 'Service'))    return 'service';
        if (str_ends_with($name, 'Repository')) return 'repository';
        if (str_contains($file, DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR) ||
            str_ends_with($name, 'Model')) return 'model';
        return 'other';
    }

    private function extractDeps(string $content): array
    {
        $deps = [];

        // Constructor type-hint injections
        if (preg_match('/public\s+function\s+__construct\s*\(([^)]*)\)/s', $content, $m)) {
            preg_match_all('/\b([A-Z]\w+(?:\\\\\w+)*)\s+\$/', $m[1], $hits);
            $deps = $hits[1] ?? [];
        }

        // `use` statement imports (catches Model imports in Repositories/Services)
        preg_match_all('/\buse\s+(?:[A-Za-z\\\\]+\\\\)([A-Z]\w+)\s*;/', $content, $useHits);
        $deps = array_merge($deps, $useHits[1] ?? []);

        return array_unique($deps);
    }
}
