<?php

namespace Vcian\Laradar;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Vcian\Laradar\Analyzers\ControllerAnalyzer;
use Vcian\Laradar\Analyzers\DependencyAnalyzer;
use Vcian\Laradar\Analyzers\EventAnalyzer;
use Vcian\Laradar\Analyzers\JobAnalyzer;
use Vcian\Laradar\Analyzers\ModelAnalyzer;
use Vcian\Laradar\Analyzers\ObserverAnalyzer;
use Vcian\Laradar\Analyzers\ApiDocAnalyzer;
use Vcian\Laradar\Analyzers\ModuleAnalyzer;
use Vcian\Laradar\Analyzers\PackageDetector;
use Vcian\Laradar\Analyzers\PolicyAnalyzer;
use Vcian\Laradar\Analyzers\RouteAnalyzer;
use Vcian\Laradar\Analyzers\ServiceAnalyzer;
use Vcian\Laradar\AI\AIManager;
use Vcian\Laradar\Http\Controllers\AIController;
use Vcian\Laradar\Http\Controllers\DashboardController;
use Vcian\Laradar\Http\Controllers\ExportController;
use Vcian\Laradar\Services\ArchitectureScanner;
use Vcian\Laradar\Services\ReportExporter;

class LaradarServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laradar.php',
            'laradar'
        );

        $this->app->singleton(ArchitectureScanner::class, function ($app) {
            $config = $app['config']->get('laradar', []);
            $scan   = $config['scan'] ?? [];
            $paths  = $config['paths'] ?? [];

            $appNamespace = $config['app_namespace'] ?? $this->detectAppNamespace();

            $analyzers = [];

            if ($scan['models'] ?? true) {
                $analyzers['models'] = new ModelAnalyzer(
                    $paths['models'] ?? $this->detectModelsPath()
                );
            }

            if ($scan['controllers'] ?? true) {
                $analyzers['controllers'] = new ControllerAnalyzer(
                    $paths['controllers'] ?? app_path('Http/Controllers')
                );
            }

            if ($scan['routes'] ?? true) {
                $analyzers['routes'] = new RouteAnalyzer($appNamespace);
            }

            if ($scan['dependencies'] ?? true) {
                $analyzers['dependencies'] = new DependencyAnalyzer(app_path());
            }

            if ($scan['jobs'] ?? true) {
                $analyzers['jobs'] = new JobAnalyzer(
                    $paths['jobs'] ?? app_path('Jobs')
                );
            }

            if ($scan['events'] ?? true) {
                $analyzers['events'] = new EventAnalyzer(
                    $paths['events'] ?? app_path('Events')
                );
            }

            if ($scan['services'] ?? true) {
                $analyzers['services'] = new ServiceAnalyzer(
                    $paths['services'] ?? app_path('Services'),
                    'Service'
                );
            }

            if ($scan['repositories'] ?? true) {
                $analyzers['repositories'] = new ServiceAnalyzer(
                    $paths['repositories'] ?? $this->detectRepositoriesPath(),
                    'Repository'
                );
            }

            if ($scan['observers'] ?? true) {
                $analyzers['observers'] = new ObserverAnalyzer(
                    $paths['observers'] ?? app_path('Observers')
                );
            }

            if ($scan['policies'] ?? true) {
                $analyzers['policies'] = new PolicyAnalyzer(
                    $paths['policies'] ?? app_path('Policies')
                );
            }

            if ($scan['modules'] ?? true) {
                $analyzers['modules'] = new ModuleAnalyzer(
                    $paths['modules'] ?? $this->detectModulesPath()
                );
            }

            if ($scan['packages'] ?? true) {
                $analyzers['packages'] = new PackageDetector(base_path());
            }

            if ($scan['api_docs'] ?? true) {
                $analyzers['api_docs'] = new ApiDocAnalyzer(
                    $appNamespace,
                    app_path()
                );
            }

            // Dead code detector — opt-out default so it runs even on published configs
            // that pre-date this key. Set dead_code => false in config to disable.
            if ($scan['dead_code'] ?? true) {
                $analyzers['dead_code'] = true;
            }

            return new ArchitectureScanner($analyzers);
        });

        $this->app->singleton(Laradar::class, function ($app) {
            return new Laradar($app->make(ArchitectureScanner::class));
        });

        $this->app->singleton(ReportExporter::class, fn() => new ReportExporter());

        $this->app->singleton(AIManager::class, function ($app) {
            return new AIManager(
                $app['config']->get('laradar.ai', [])
            );
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laradar');

        $this->registerDashboardRoute();

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\LaradarCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/laradar.php' => config_path('laradar.php'),
            ], 'laradar-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laradar'),
            ], 'laradar-views');
        }
    }

    private function registerDashboardRoute(): void
    {
        $config = config('laradar.dashboard', []);

        if (!($config['enabled'] ?? true)) {
            return;
        }

        if (!$this->app->environment('local', 'development')) {
            return;
        }

        $path       = $config['path']       ?? 'architecture';
        $middleware = $config['middleware']  ?? ['web'];

        Route::middleware($middleware)
            ->get($path, DashboardController::class)
            ->name('laradar.dashboard');

        Route::middleware($middleware)
            ->get($path . '/export/{format}', ExportController::class)
            ->name('laradar.export')
            ->where('format', 'html|svg');

        Route::middleware($middleware)->group(function () use ($path) {
            Route::post($path . '/ai/analyze',       [AIController::class, 'analyze'])->name('laradar.ai.analyze');
            Route::post($path . '/ai/chat',          [AIController::class, 'chat'])->name('laradar.ai.chat');
            Route::post($path . '/ai/documentation', [AIController::class, 'documentation'])->name('laradar.ai.documentation');
        });
    }

    private function detectModulesPath(): string
    {
        foreach (['Modules', 'modules', 'src/Modules'] as $dir) {
            $path = base_path($dir);
            if (is_dir($path)) return $path;
        }
        return base_path('Modules');
    }

    private function detectRepositoriesPath(): string
    {
        foreach (['Repositories', 'Repository', 'Repos'] as $dir) {
            if (is_dir(app_path($dir))) return app_path($dir);
        }
        return app_path('Repositories');
    }

    private function detectModelsPath(): string
    {
        if (is_dir(app_path('Models'))) {
            return app_path('Models');
        }

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
