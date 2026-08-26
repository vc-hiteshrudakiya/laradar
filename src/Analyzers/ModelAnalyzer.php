<?php

namespace Vcian\Laradar\Analyzers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class ModelAnalyzer
{
    private const RELATIONSHIP_TYPES = [
        'hasMany', 'hasOne', 'belongsTo', 'belongsToMany',
        'hasManyThrough', 'hasOneThrough',
        'morphTo', 'morphMany', 'morphOne', 'morphToMany',
    ];

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
                $item = $this->processFile($file);
                if ($item !== null) {
                    $items[] = $item;
                }
            } catch (\Throwable $e) {
                $errors[] = [
                    'file'    => $this->relativePath($file->getRealPath()),
                    'message' => $e->getMessage(),
                ];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    private function processFile(SplFileInfo $file): ?array
    {
        $content = file_get_contents($file->getRealPath());

        // Skip interfaces, traits, and abstract classes — not concrete Eloquent models
        if (preg_match('/^\s*(?:interface|trait|abstract\s+class)\s+\w/m', $content)) {
            return null;
        }

        $modelName = $file->getFilenameWithoutExtension();
        $namespace = $this->detectNamespace($content);

        return [
            'name'          => $modelName,
            'path'          => $this->relativePath($file->getRealPath()),
            'namespace'     => $namespace,
            'full_class'    => $namespace ? $namespace . '\\' . $modelName : $modelName,
            'table'         => $this->detectTable($content, $modelName),
            'primary_key'   => $this->detectStringProperty($content, 'primaryKey', 'id'),
            'key_type'      => $this->detectStringProperty($content, 'keyType', 'int'),
            'incrementing'  => $this->detectBoolProperty($content, 'incrementing', true),
            'timestamps'    => $this->detectBoolProperty($content, 'timestamps', true),
            'date_format'   => $this->detectStringProperty($content, 'dateFormat', null),
            'connection'    => $this->detectStringProperty($content, 'connection', null),
            'fillable'      => $this->detectArrayProperty($content, 'fillable'),
            'guarded'       => $this->detectArrayProperty($content, 'guarded'),
            'hidden'        => $this->detectArrayProperty($content, 'hidden'),
            'appends'       => $this->detectArrayProperty($content, 'appends'),
            'with'          => $this->detectArrayProperty($content, 'with'),
            'casts'         => $this->detectCasts($content),
            'relationships' => $this->detectRelationships($content),
            'traits'        => $this->detectTraits($content),
            'observer'      => $this->detectObserver($content),
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

    private function detectStringProperty(string $content, string $property, ?string $default): ?string
    {
        preg_match('/(?:public|protected)\s+(?:string\s+)?\$' . $property . '\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $match);
        return $match[1] ?? $default;
    }

    private function detectBoolProperty(string $content, string $property, bool $default): bool
    {
        if (preg_match('/(?:public|protected)\s+(?:bool\s+)?\$' . $property . '\s*=\s*(true|false)\b/i', $content, $match)) {
            return strtolower($match[1]) === 'true';
        }
        return $default;
    }

    private function detectTable(string $content, string $modelName): string
    {
        preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"]/m', $content, $match);
        return $match[1] ?? Str::snake(Str::pluralStudly($modelName));
    }

    private function detectArrayProperty(string $content, string $property): array
    {
        preg_match('/protected\s+\$' . $property . '\s*=\s*\[(.*?)\]/s', $content, $match);

        if (empty($match[1])) {
            return [];
        }

        preg_match_all('/[\'"]([^\'"]+)[\'"]/', $match[1], $strings);
        return $strings[1] ?? [];
    }

    private function detectCasts(string $content): array
    {
        preg_match('/protected\s+\$casts\s*=\s*\[(.*?)\]/s', $content, $match);

        if (empty($match[1])) {
            return [];
        }

        preg_match_all('/[\'"]([^\'"]+)[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $match[1], $pairs);

        $casts = [];
        foreach ($pairs[1] as $i => $key) {
            $casts[$key] = $pairs[2][$i];
        }

        return $casts;
    }

    private function detectRelationships(string $content): array
    {
        $relationships = [];

        foreach (self::RELATIONSHIP_TYPES as $type) {
            preg_match_all(
                '/public\s+function\s+(\w+)\s*\(\s*\)[^{]*\{[^}]*return\s+\$this->' . $type . '\s*\(\s*([A-Za-z_\\\\]+)::class/s',
                $content,
                $matches
            );

            foreach ($matches[1] as $i => $method) {
                $related = class_basename(str_replace('\\', '/', $matches[2][$i]));
                $relationships[] = [
                    'type'    => $type,
                    'method'  => $method,
                    'related' => $related,
                ];
            }
        }

        return $relationships;
    }

    private function detectTraits(string $content): array
    {
        // Find the class body start (after the class declaration line)
        $classPos = strpos($content, '{');
        if ($classPos === false) return [];
        $body = substr($content, $classPos);

        // Match use statements inside class body (traits, not imports)
        // Traits start with uppercase; imports would have been in file header
        $traits = [];
        if (preg_match('/\buse\s+([\w,\s\\\\]+?)\s*;/s', $body, $m)) {
            $parts = preg_split('/\s*,\s*/', $m[1]);
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '') continue;
                $traits[] = class_basename($part);
            }
        }
        return $traits;
    }

    private function detectObserver(string $content): ?string
    {
        // #[ObservedBy(UserObserver::class)]
        if (preg_match('/#\[ObservedBy\(([A-Za-z]+)::class\)\]/', $content, $m)) {
            return $m[1];
        }
        return null;
    }
}
