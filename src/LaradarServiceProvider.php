<?php

namespace Vcian\Laradar;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Vcian\Laradar\Analyzers\ControllerAnalyzer;
use Vcian\Laradar\Analyzers\EventAnalyzer;
use Vcian\Laradar\Analyzers\JobAnalyzer;
use Vcian\Laradar\Analyzers\MigrationAnalyzer;
use Vcian\Laradar\Analyzers\ModelAnalyzer;
use Vcian\Laradar\Analyzers\ObserverAnalyzer;
use Vcian\Laradar\Analyzers\ModuleAnalyzer;
use Vcian\Laradar\Analyzers\PackageDetector;
use Vcian\Laradar\Analyzers\PolicyAnalyzer;
use Vcian\Laradar\Analyzers\RouteAnalyzer;
use Vcian\Laradar\Analyzers\ServiceAnalyzer;
use Vcian\Laradar\AI\AIManager;
use Vcian\Laradar\Http\Controllers\AIController;
use Vcian\Laradar\Http\Controllers\DashboardController;
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

            if ($scan['migrations'] ?? true) {
                $analyzers['migrations'] = new MigrationAnalyzer(
                    $paths['migrations'] ?? database_path('migrations')
                );
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

        $path       = $config['path']       ?? 'laradar';
        $middleware = $config['middleware']  ?? ['web'];

        // Overview (root)
        Route::middleware($middleware)
            ->get($path, [DashboardController::class, 'overview'])
            ->name('laradar.dashboard');

        // Per-section pages
        foreach (['models','controllers','routes','migrations','jobs','events','services',
                  'repositories','observers','policies','modules','middleware',
                  'packages','ai','chat','aidocs'] as $section) {
            $method = $section === 'middleware' ? 'middlewarePage' : $section;
            Route::middleware($middleware)
                ->get($path . '/' . $section, [DashboardController::class, $method])
                ->name('laradar.' . $section);
        }

        // Model detail page
        Route::middleware($middleware)
            ->get($path . '/models/{model}', [DashboardController::class, 'modelDetail'])
            ->name('laradar.model.detail')
            ->where('model', '[a-zA-Z0-9_]+');

        // Serve the last generated HTML scan report
        Route::middleware($middleware)
            ->get($path . '/report', function () {
                $file = storage_path('architecture/report.html');
                if (!file_exists($file)) {
                    abort(404, 'No report found. Run php artisan laradar:scan first.');
                }
                return response(file_get_contents($file), 200, ['Content-Type' => 'text/html']);
            })
            ->name('laradar.report');

        Route::middleware($middleware)->group(function () use ($path) {
            Route::post($path . '/ai/analyze',       [AIController::class, 'analyze'])->name('laradar.ai.analyze');
            Route::post($path . '/ai/chat',          [AIController::class, 'chat'])->name('laradar.ai.chat');
            Route::post($path . '/ai/documentation', [AIController::class, 'documentation'])->name('laradar.ai.documentation');
        });

        // Serve package static assets (icon, favicons) without requiring vendor:publish
        Route::get($path . '/assets/{filename}', function (string $filename) {
            $file = realpath(__DIR__ . '/../public/' . $filename);
            $base = realpath(__DIR__ . '/../public');
            if (!$file || !str_starts_with($file, $base) || !file_exists($file)) {
                abort(404);
            }
            $mime = match(pathinfo($filename, PATHINFO_EXTENSION)) {
                'svg'  => 'image/svg+xml',
                'ico'  => 'image/x-icon',
                'png'  => 'image/png',
                default => 'application/octet-stream',
            };
            return response()->file($file, ['Content-Type' => $mime, 'Cache-Control' => 'public, max-age=86400']);
        })->name('laradar.asset')->where('filename', '[a-zA-Z0-9_\-\.]+');
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
