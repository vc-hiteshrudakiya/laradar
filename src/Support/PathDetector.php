<?php

namespace Vcian\Laradar\Support;

class PathDetector
{
    public static function modelsPath(): string
    {
        return is_dir(app_path('Models')) ? app_path('Models') : app_path();
    }

    public static function repositoriesPath(): string
    {
        foreach (['Repositories', 'Repository', 'Repos'] as $dir) {
            if (is_dir(app_path($dir))) {
                return app_path($dir);
            }
        }

        return app_path('Repositories');
    }

    public static function modulesPath(): string
    {
        foreach (['Modules', 'modules', 'src/Modules'] as $dir) {
            $path = base_path($dir);
            if (is_dir($path)) {
                return $path;
            }
        }

        return base_path('Modules');
    }

    public static function appNamespace(): string
    {
        try {
            $composer = json_decode(
                file_get_contents(base_path('composer.json')),
                true
            );

            foreach ($composer['autoload']['psr-4'] ?? [] as $namespace => $path) {
                foreach ((array) $path as $p) {
                    if (rtrim($p, '/') === 'app') {
                        return rtrim($namespace, '\\');
                    }
                }
            }
        } catch (\Throwable) {
            // fall through to default
        }

        return 'App';
    }
}
