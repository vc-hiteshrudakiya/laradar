<?php

namespace Vcian\Laradar\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class JobAnalyzer
{
    public function __construct(private string $path) {}

    public function analyze(): array
    {
        if (!is_dir($this->path)) {
            return ['items' => [], 'errors' => []];
        }

        $items = $errors = [];

        foreach (File::allFiles($this->path) as $file) {
            if ($file->getExtension() !== 'php') continue;
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

        // Queue / retry configuration
        $queue = null;
        if (preg_match('/\$queue\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $qm)) {
            $queue = $qm[1];
        }

        $tries = null;
        if (preg_match('/\$tries\s*=\s*(\d+)/', $content, $tm)) {
            $tries = (int) $tm[1];
        }

        $timeout = null;
        if (preg_match('/\$timeout\s*=\s*(\d+)/', $content, $tom)) {
            $timeout = (int) $tom[1];
        }

        $delay = null;
        if (preg_match('/\$delay\s*=\s*(\d+)/', $content, $dm)) {
            $delay = (int) $dm[1];
        }

        $maxExceptions = null;
        if (preg_match('/\$maxExceptions\s*=\s*(\d+)/', $content, $mem)) {
            $maxExceptions = (int) $mem[1];
        }

        $backoff = null;
        if (preg_match('/\$backoff\s*=\s*(\d+)/', $content, $bom)) {
            $backoff = (int) $bom[1];
        }

        $connection = null;
        if (preg_match('/\$connection\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $com)) {
            $connection = $com[1];
        }

        // Constructor dependencies (what data the job carries)
        $dependencies = [];
        if (preg_match('/public\s+function\s+__construct\s*\(([^)]*)\)/s', $content, $ctor)) {
            preg_match_all('/(?:public|protected|private|readonly)?\s*(?:\?)?([A-Z][\w\\\\]+)\s+\$(\w+)/', $ctor[1], $pm, PREG_SET_ORDER);
            foreach ($pm as $p) {
                $dependencies[] = ['type' => class_basename($p[1]), 'name' => $p[2]];
            }
        }

        // handle() method injected dependencies (resolved from container, not constructor)
        $handleDeps = [];
        if (preg_match('/public\s+function\s+handle\s*\(([^)]+)\)/s', $content, $hm)) {
            preg_match_all('/(?:\?)?([A-Z][\w\\\\]+)\s+\$(\w+)/', $hm[1], $hp, PREG_SET_ORDER);
            foreach ($hp as $p) {
                $handleDeps[] = ['type' => class_basename($p[1]), 'name' => $p[2]];
            }
        }

        // Job middleware (WithoutOverlapping, RateLimited, etc.)
        $jobMiddleware = [];
        if (preg_match('/public\s+function\s+middleware\s*\(\s*\)[^{]*\{(.*?)\}/s', $content, $mwm)) {
            preg_match_all('/new\s+([\w\\\\]+)\s*[,()\]]/s', $mwm[1], $mwc);
            foreach ($mwc[1] as $mw) {
                $jobMiddleware[] = class_basename($mw);
            }
        }

        return [
            'name'           => $name,
            'namespace'      => $namespace,
            'path'           => $this->relativePath($file->getRealPath()),
            'should_queue'   => str_contains($content, 'ShouldQueue'),
            'unique'         => str_contains($content, 'ShouldBeUnique'),
            'encrypted'      => str_contains($content, 'ShouldBeEncrypted'),
            'has_failed'     => str_contains($content, 'public function failed'),
            'queue'          => $queue,
            'connection'     => $connection,
            'tries'          => $tries,
            'timeout'        => $timeout,
            'delay'          => $delay,
            'backoff'        => $backoff,
            'max_exceptions' => $maxExceptions,
            'dependencies'   => $dependencies,
            'handle_deps'    => $handleDeps,
            'job_middleware' => $jobMiddleware,
        ];
    }

    private function relativePath(string $absolutePath): string
    {
        return ltrim(str_replace(base_path(), '', $absolutePath), DIRECTORY_SEPARATOR);
    }
}
