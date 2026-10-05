<?php

namespace Vcian\Laradar\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class ServiceAnalyzer
{
    private const SKIP_METHODS = ['__construct', '__destruct', '__toString', '__invoke'];

    public function __construct(
        private string $path,
        private string $suffix = 'Service',
    ) {}

    public function analyze(): array
    {
        if (!is_dir($this->path)) {
            return ['items' => [], 'errors' => []];
        }

        $items = $errors = [];

        foreach (File::allFiles($this->path) as $file) {
            if ($file->getExtension() !== 'php') continue;
            if (!str_ends_with($file->getFilenameWithoutExtension(), $this->suffix)) continue;
            try {
                $item = $this->processFile($file);
                if ($item) $items[] = $item;
            } catch (\Throwable $e) {
                $errors[] = ['file' => $file->getRelativePathname(), 'message' => $e->getMessage()];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    private function processFile(SplFileInfo $file): ?array
    {
        $content = file_get_contents($file->getRealPath());

        if (!preg_match('/^namespace\s+([^;]+);/m', $content, $ns)) return null;
        if (!preg_match('/class\s+(\w+)/', $content, $cn)) return null;

        $name      = $cn[1];
        $namespace = trim($ns[1]);

        // Constructor dependencies
        $dependencies = [];
        if (preg_match('/public\s+function\s+__construct\s*\(([^)]*)\)/s', $content, $ctor)) {
            preg_match_all('/(?:public|protected|private|readonly)?\s*(?:\?)?([A-Z][\w\\\\]+)\s+\$(\w+)/', $ctor[1], $pm, PREG_SET_ORDER);
            foreach ($pm as $p) {
                $dependencies[] = class_basename($p[1]);
            }
        }

        // Public methods (skip magic) — kept for backward compatibility
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $mm);
        $methods = array_values(array_filter($mm[1] ?? [], fn($m) => !in_array($m, self::SKIP_METHODS)));

        // Method details: name + parameters + return type
        $methodDetails = $this->parseMethodDetails($content);

        // Implements clause: class Foo extends Bar implements IFoo, IBar
        $implements = [];
        if (preg_match('/class\s+\w+(?:\s+extends\s+[\w\\\\]+)?\s+implements\s+([\w\s,\\\\]+?)(?:\{|$)/m', $content, $im)) {
            foreach (explode(',', $im[1]) as $iface) {
                $iface = trim($iface);
                if ($iface !== '') {
                    $implements[] = class_basename($iface);
                }
            }
        }

        return [
            'name'           => $name,
            'namespace'      => $namespace,
            'path'           => $this->relativePath($file->getRealPath()),
            'dependencies'   => $dependencies,
            'methods'        => $methods,
            'method_count'   => count($methods),
            'method_details' => $methodDetails,
            'implements'     => $implements,
        ];
    }

    private function parseMethodDetails(string $content): array
    {
        preg_match_all(
            '/public\s+function\s+(\w+)\s*\(([^)]*)\)\s*(?::\s*(\??[\w\\\\|]+))?/s',
            $content,
            $matches,
            PREG_SET_ORDER
        );

        $details = [];

        foreach ($matches as $match) {
            $name = $match[1];

            if (in_array($name, self::SKIP_METHODS)) {
                continue;
            }

            // Parse parameters
            $params = [];
            $sig    = trim($match[2] ?? '');

            if ($sig !== '') {
                foreach (preg_split('/,(?![^<>]*>)/', $sig) as $param) {
                    $param = trim($param);
                    if ($param === '') continue;

                    if (preg_match('/\??([A-Z][\w\\\\]+(?:[|&]\??[A-Z][\w\\\\]+)*)\s+\$(\w+)/', $param, $p)) {
                        $primary  = preg_split('/[|&]/', ltrim($p[1], '?'))[0];
                        $params[] = ['type' => class_basename(str_replace('\\', '/', $primary)), 'var' => $p[2]];
                    } elseif (preg_match('/([a-z]+(?:\|[a-z]+)*)\s+\$(\w+)/', $param, $p)) {
                        $params[] = ['type' => $p[1], 'var' => $p[2]];
                    } elseif (preg_match('/\$(\w+)/', $param, $p)) {
                        $params[] = ['type' => null, 'var' => $p[1]];
                    }
                }
            }

            // Parse return type
            $returnRaw  = trim($match[3] ?? '');
            $returnType = $returnRaw !== ''
                ? class_basename(str_replace('\\', '/', ltrim($returnRaw, '?')))
                : null;
            $returnNullable = str_starts_with($returnRaw, '?');

            $details[] = [
                'method'          => $name,
                'params'          => $params,
                'return_type'     => $returnType,
                'return_nullable' => $returnNullable,
            ];
        }

        return $details;
    }

    private function relativePath(string $absolutePath): string
    {
        return ltrim(str_replace(base_path(), '', $absolutePath), DIRECTORY_SEPARATOR);
    }
}
