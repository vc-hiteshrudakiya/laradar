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

        $items    = [];
        $errors   = [];
        $traitMap = [];

        $allFiles = File::allFiles($this->path);

        // First pass — build a map of trait name → file content
        foreach ($allFiles as $file) {
            if ($file->getExtension() !== 'php') continue;
            $content = file_get_contents($file->getRealPath());
            if (preg_match('/^\s*trait\s+\w/m', $content)) {
                $traitMap[$file->getFilenameWithoutExtension()] = $content;
            }
        }

        // Second pass — process concrete model files
        foreach ($allFiles as $file) {
            if ($file->getExtension() !== 'php') continue;
            try {
                $item = $this->processFile($file, $traitMap);
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

    private function processFile(SplFileInfo $file, array $traitMap = []): ?array
    {
        $content = file_get_contents($file->getRealPath());

        // Skip interfaces, traits, and abstract classes — not concrete Eloquent models
        if (preg_match('/^\s*(?:interface|trait|abstract\s+class)\s+\w/m', $content)) {
            return null;
        }

        $modelName  = $file->getFilenameWithoutExtension();
        $namespace  = $this->detectNamespace($content);
        $traits     = $this->detectTraits($content);

        // Append content of each used trait so relationship detection covers trait-defined relations
        $combined = $content;
        foreach ($traits as $traitName) {
            if (isset($traitMap[$traitName])) {
                $combined .= "\n" . $traitMap[$traitName];
            }
        }

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
            'relationships' => $this->detectRelationships($combined, $modelName),
            'traits'        => $traits,
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
        // Property style: protected $casts = [...]
        preg_match('/protected\s+\$casts\s*=\s*\[(.*?)\]/s', $content, $match);

        // Method style (Laravel 11): protected function casts(): array { return [...]; }
        if (empty($match[1])) {
            preg_match('/protected\s+function\s+casts\s*\(\s*\)\s*(?::\s*\w+\s*)?\{.*?return\s*\[(.*?)\]\s*;/s', $content, $match);
        }

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

    private function detectRelationships(string $content, string $modelName): array
    {
        $relationships = [];
        $modelSnake    = Str::snake($modelName);

        foreach (self::RELATIONSHIP_TYPES as $type) {
            // Pass 1: standard ::class syntax
            preg_match_all(
                '/public\s+function\s+(\w+)\s*\(\s*\)[^{]*\{[^}]*return\s+\$this->' . $type . '\s*\(\s*([A-Za-z_\\\\]+)::class\s*(?:,\s*[\'"]([^\'"]+)[\'"])?/s',
                $content,
                $matches
            );

            foreach ($matches[1] as $i => $method) {
                $related    = class_basename(str_replace('\\', '/', $matches[2][$i]));
                $explicitFk = ($matches[3][$i] ?? '') !== '' ? $matches[3][$i] : null;

                if (!$explicitFk) {
                    if ($type === 'belongsTo') {
                        $explicitFk = Str::snake($related) . '_id';
                    } elseif (in_array($type, ['hasMany', 'hasOne', 'hasManyThrough'])) {
                        $explicitFk = $modelSnake . '_id';
                    }
                }

                $relationships[] = [
                    'type'        => $type,
                    'method'      => $method,
                    'related'     => $related,
                    'foreign_key' => $explicitFk,
                    'dynamic'     => false,
                ];
            }

            // Pass 2: config()-based syntax e.g. $this->hasOne(config('x.y.model.class'), 'fk')
            preg_match_all(
                '/public\s+function\s+(\w+)\s*\(\s*\)[^{]*\{[^}]*return\s+\$this->' . $type . '\s*\(\s*config\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)\s*(?:,\s*[\'"]([^\'"]+)[\'"])?/s',
                $content,
                $configMatches
            );

            foreach ($configMatches[1] as $i => $method) {
                $configKey  = $configMatches[2][$i];
                $parts      = array_filter(explode('.', $configKey), fn($p) => $p !== 'class');
                $related    = Str::studly(end($parts) ?: $configKey);
                $explicitFk = ($configMatches[3][$i] ?? '') !== '' ? $configMatches[3][$i] : null;

                if (!$explicitFk) {
                    if ($type === 'belongsTo') {
                        $explicitFk = Str::snake($related) . '_id';
                    } elseif (in_array($type, ['hasMany', 'hasOne', 'hasManyThrough'])) {
                        $explicitFk = $modelSnake . '_id';
                    }
                }

                $relationships[] = [
                    'type'        => $type,
                    'method'      => $method,
                    'related'     => $related,
                    'foreign_key' => $explicitFk,
                    'dynamic'     => true,
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

        // Match ALL use statements inside class body (handles both single-line and multi-line declarations)
        $traits = [];
        preg_match_all('/\buse\s+([\w,\s\\\\]+?)\s*;/s', $body, $matches);
        foreach ($matches[1] as $group) {
            $parts = preg_split('/\s*,\s*/', $group);
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '') continue;
                $traits[] = class_basename($part);
            }
        }
        return array_unique($traits);
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
