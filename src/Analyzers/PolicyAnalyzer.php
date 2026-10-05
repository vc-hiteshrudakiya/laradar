<?php

namespace Vcian\Laradar\Analyzers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class PolicyAnalyzer
{
    private const POLICY_ACTIONS = [
        'before', 'viewAny', 'view', 'create', 'update',
        'delete', 'restore', 'forceDelete',
    ];

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

        // Infer model name from policy class name (UserPolicy → User)
        $model = str_ends_with($name, 'Policy') ? substr($name, 0, -6) : null;

        // Detect implemented standard policy actions
        $actions = [];
        foreach (self::POLICY_ACTIONS as $action) {
            if (preg_match('/public\s+function\s+' . preg_quote($action, '/') . '\s*\(/', $content)) {
                $actions[] = $action;
            }
        }

        // Detect custom (non-standard) public methods
        preg_match_all('/public\s+function\s+(\w+)\s*\(/', $content, $allMethods);
        $customActions = array_values(array_filter(
            $allMethods[1] ?? [],
            fn($m) => !in_array($m, self::POLICY_ACTIONS) && !str_starts_with($m, '__')
        ));

        return [
            'name'           => $name,
            'namespace'      => $namespace,
            'path'           => $file->getRelativePathname(),
            'model'          => $model,
            'actions'        => $actions,
            'custom_actions' => $customActions,
        ];
    }
}
