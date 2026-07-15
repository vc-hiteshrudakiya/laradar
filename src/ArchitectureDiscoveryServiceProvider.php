<?php

namespace Hitesh\LaravelArchitectureDiscovery;

use Illuminate\Support\ServiceProvider;
use Hitesh\LaravelArchitectureDiscovery\Services\ArchitectureScanner;
use Hitesh\LaravelArchitectureDiscovery\Services\ReportExporter;
use Hitesh\LaravelArchitectureDiscovery\Analyzers\ModelAnalyzer;
use Hitesh\LaravelArchitectureDiscovery\Analyzers\RouteAnalyzer;
use Hitesh\LaravelArchitectureDiscovery\Analyzers\ControllerAnalyzer;
use Hitesh\LaravelArchitectureDiscovery\Analyzers\DependencyAnalyzer;

class ArchitectureDiscoveryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/architecture-discovery.php',
            'architecture-discovery'
        );

        $this->app->singleton(ArchitectureScanner::class, function ($app) {
            $config = $app['config']->get('architecture-discovery', []);
            $scan   = $config['scan'] ?? [];
            $paths  = $config['paths'] ?? [];

            $appNamespace = $config['app_namespace'] ?? $this->detectAppNamespace();

            $analyzers = [];

            if ($scan['models'] ?? true) {
                $modelsPath = $paths['models'] ?? $this->detectModelsPath();
                $analyzers['models'] = new ModelAnalyzer($modelsPath);
            }

            if ($scan['controllers'] ?? true) {
                $controllersPath = $paths['controllers'] ?? app_path('Http/Controllers');
                $analyzers['controllers'] = new ControllerAnalyzer($controllersPath);
            }

            if ($scan['routes'] ?? true) {
                $analyzers['routes'] = new RouteAnalyzer($appNamespace);
            }

            if ($scan['dependencies'] ?? true) {
                $analyzers['dependencies'] = new DependencyAnalyzer(app_path());
            }

            return new ArchitectureScanner($analyzers);
        });

        $this->app->singleton(ArchitectureDiscovery::class, function ($app) {
            return new ArchitectureDiscovery($app->make(ArchitectureScanner::class));
        });

        $this->app->singleton(ReportExporter::class, fn() => new ReportExporter());
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'architecture-discovery');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\DiscoverArchitectureCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/architecture-discovery.php' => config_path('architecture-discovery.php'),
            ], 'architecture-discovery-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/architecture-discovery'),
            ], 'architecture-discovery-views');
        }
    }

    private function detectModelsPath(): string
    {
        // Laravel 8+ standard
        if (is_dir(app_path('Models'))) {
            return app_path('Models');
        }

        // Laravel < 8 — models live directly in app/
        return app_path();
    }

    private function detectAppNamespace(): string
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
        } catch (\Throwable $e) {
            // fall through to default
        }

        return 'App';
    }
}
