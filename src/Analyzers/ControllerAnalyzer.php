<?php

namespace Hitesh\LaravelArchitectureDiscovery\Analyzers;

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
            'name'         => $name,
            'path'         => $this->relativePath($file->getRealPath()),
            'namespace'    => $namespace,
            'full_class'   => $namespace ? $namespace . '\\' . $name : $name,
            'methods'      => $methods,
            'method_count' => count($methods),
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
}
