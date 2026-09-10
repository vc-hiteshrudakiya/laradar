<?php

namespace Vcian\Laradar;

use Illuminate\Support\ServiceProvider;
use Vcian\Laradar\AI\AIManager;
use Vcian\Laradar\Services\ArchitectureScanner;
use Vcian\Laradar\Services\ReportExporter;
use Vcian\Laradar\Support\ArchitectureScannerFactory;

class LaradarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laradar.php', 'laradar');

        $this->app->singleton(ArchitectureScanner::class, function ($app) {
            return ArchitectureScannerFactory::make($app['config']->get('laradar', []));
        });

        $this->app->singleton(Laradar::class, function ($app) {
            return new Laradar($app->make(ArchitectureScanner::class));
        });

        $this->app->singleton(ReportExporter::class, fn() => new ReportExporter());

        $this->app->singleton(AIManager::class, function ($app) {
            return new AIManager($app['config']->get('laradar.ai', []));
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laradar');

        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->commands([Commands\LaradarCommand::class]);

            $this->publishes([
                __DIR__ . '/../config/laradar.php' => config_path('laradar.php'),
            ], 'laradar-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laradar'),
            ], 'laradar-views');

            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/laradar'),
            ], 'laradar-assets');
        }
    }

    private function registerRoutes(): void
    {
        $config = config('laradar.dashboard', []);

        if (!($config['enabled'] ?? true)) {
            return;
        }

        if (!$this->app->environment('local', 'development', 'testing')) {
            return;
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
