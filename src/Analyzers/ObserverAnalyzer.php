<?php

namespace Vcian\Laradar\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class ObserverAnalyzer
{
    private const LIFECYCLE_EVENTS = [
        'retrieved', 'creating', 'created', 'updating', 'updated',
        'saving', 'saved', 'deleting', 'deleted', 'restoring',
        'restored', 'forceDeleting', 'forceDeleted',
    ];

    public function __construct(private string $path) {}

    public function analyze(): array
    {
        if (!is_dir($this->path)) {
            return ['items' => [], 'errors' => []];
        }

        // Scan service providers to detect Model::observe() registrations
        $observeMap = $this->buildObserveMap();
        $items      = $errors = [];

        foreach (File::allFiles($this->path) as $file) {
            if ($file->getExtension() !== 'php') continue;
            try {
                $item = $this->processFile($file, $observeMap);
                if ($item) $items[] = $item;
            } catch (\Throwable $e) {
                $errors[] = ['file' => $file->getRelativePathname(), 'message' => $e->getMessage()];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    // Scan all service providers for Model::observe(ObserverClass::class) calls
    private function buildObserveMap(): array
    {
        $map           = [];
        $providerPaths = glob(app_path('Providers') . '/*.php') ?: [];

        foreach ($providerPaths as $providerPath) {
            $content = @file_get_contents($providerPath);
            if (!$content) continue;

            // Match: SomeModel::observe(SomeObserver::class)
            preg_match_all(
                '/([A-Za-z][\w\\\\]+)::observe\s*\(\s*([A-Za-z][\w\\\\]+)::class\s*\)/',
                $content,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $match) {
                $observer = class_basename(str_replace('\\', '/', $match[2]));
                $map[$observer] = 'ServiceProvider';
            }
        }

        return $map;
    }

    private function processFile(SplFileInfo $file, array $observeMap = []): ?array
    {
        $content = file_get_contents($file->getRealPath());

        if (!preg_match('/^namespace\s+([^;]+);/m', $content, $ns)) return null;
        if (!preg_match('/class\s+(\w+)/', $content, $cn)) return null;

        $name      = $cn[1];
        $namespace = trim($ns[1]);

        // Infer model name from observer class name (UserObserver → User)
        $model = str_ends_with($name, 'Observer') ? substr($name, 0, -8) : null;

        // Detect handled lifecycle events
        $events = [];
        foreach (self::LIFECYCLE_EVENTS as $event) {
            if (preg_match('/public\s+function\s+' . preg_quote($event, '/') . '\s*\(/', $content)) {
                $events[] = $event;
            }
        }

        // Constructor dependencies
        $dependencies = [];
        if (preg_match('/public\s+function\s+__construct\s*\(([^)]*)\)/s', $content, $ctor)) {
            preg_match_all('/(?:public|protected|private|readonly)?\s*(?:\?)?([A-Z][\w\\\\]+)\s+\$(\w+)/', $ctor[1], $pm, PREG_SET_ORDER);
            foreach ($pm as $p) {
                $dependencies[] = ['type' => class_basename($p[1]), 'name' => $p[2]];
            }
        }

        // Registration method: #[ObservedBy] attribute on model, ServiceProvider observe(), or unknown
        $registration = 'Unknown';
        if (str_contains($content, '#[ObservedBy]') || str_contains($content, 'ObservedBy(')) {
            $registration = 'Attribute (#[ObservedBy])';
        } elseif (isset($observeMap[$name])) {
            $registration = $observeMap[$name];
        }

        return [
            'name'         => $name,
            'namespace'    => $namespace,
            'path'         => $file->getRelativePathname(),
            'model'        => $model,
            'events'       => $events,
            'should_queue' => str_contains($content, 'ShouldQueue'),
            'dependencies' => $dependencies,
            'registration' => $registration,
        ];
    }
}
