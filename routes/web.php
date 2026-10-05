<?php

use Illuminate\Support\Facades\Route;
use Vcian\Laradar\Http\Controllers\AIController;
use Vcian\Laradar\Http\Controllers\DashboardController;

$config     = config('laradar.dashboard', []);
$path       = $config['path']       ?? 'laradar';
$middleware = $config['middleware']  ?? ['web'];

Route::middleware($middleware)->group(function () use ($path) {

    // Overview (root)
    Route::get($path, [DashboardController::class, 'section'])
        ->name('laradar.dashboard')
        ->defaults('section', 'overview');

    // Per-section pages — all handled by one controller method
    foreach (['models', 'controllers', 'routes', 'migrations', 'jobs', 'events', 'services',
              'repositories', 'observers', 'policies', 'modules', 'middleware',
              'packages', 'ai', 'chat', 'aidocs', 'guide'] as $section) {
        Route::get($path . '/' . $section, [DashboardController::class, 'section'])
            ->name('laradar.' . $section)
            ->defaults('section', $section);
    }

    // Model detail page
    Route::get($path . '/models/{model}', [DashboardController::class, 'modelDetail'])
        ->name('laradar.model.detail')
        ->where('model', '[a-zA-Z0-9_]+');

    // Controller detail page
    Route::get($path . '/controllers/{controller}', [DashboardController::class, 'controllerDetail'])
        ->name('laradar.controller.detail')
        ->where('controller', '[a-zA-Z0-9_]+');

    // Route detail page
    Route::get($path . '/routes/{route}', [DashboardController::class, 'routeDetail'])
        ->name('laradar.route.detail')
        ->where('route', '[a-zA-Z0-9_.\-\/]+');

    // Migration detail page
    Route::get($path . '/migrations/{migration}', [DashboardController::class, 'migrationDetail'])
        ->name('laradar.migration.detail')
        ->where('migration', '[a-zA-Z0-9_]+');

    // Service detail page
    Route::get($path . '/services/{service}', [DashboardController::class, 'serviceDetail'])
        ->name('laradar.service.detail')
        ->where('service', '[a-zA-Z0-9_]+');

    // Repository detail page
    Route::get($path . '/repositories/{repository}', [DashboardController::class, 'repositoryDetail'])
        ->name('laradar.repository.detail')
        ->where('repository', '[a-zA-Z0-9_]+');

    // Job detail page
    Route::get($path . '/jobs/{job}', [DashboardController::class, 'jobDetail'])
        ->name('laradar.job.detail')
        ->where('job', '[a-zA-Z0-9_]+');

    // Event detail page
    Route::get($path . '/events/{event}', [DashboardController::class, 'eventDetail'])
        ->name('laradar.event.detail')
        ->where('event', '[a-zA-Z0-9_]+');

    // Observer detail page
    Route::get($path . '/observers/{observer}', [DashboardController::class, 'observerDetail'])
        ->name('laradar.observer.detail')
        ->where('observer', '[a-zA-Z0-9_]+');

    // Policy detail page
    Route::get($path . '/policies/{policy}', [DashboardController::class, 'policyDetail'])
        ->name('laradar.policy.detail')
        ->where('policy', '[a-zA-Z0-9_]+');

    // Module detail page
    Route::get($path . '/modules/{module}', [DashboardController::class, 'moduleDetail'])
        ->name('laradar.module.detail')
        ->where('module', '[a-zA-Z0-9_\-]+');

    // Middleware detail page — constraint allows any name incl. full class names with backslashes
    Route::get($path . '/middleware/{mw}', [DashboardController::class, 'middlewareDetail'])
        ->name('laradar.middleware.detail')
        ->where('mw', '[^/]+');

    // Package detail page
    Route::get($path . '/packages/{package}', [DashboardController::class, 'packageDetail'])
        ->name('laradar.package.detail')
        ->where('package', '[a-zA-Z0-9_\-\.]+');

    // Serve the last generated HTML scan report
    Route::get($path . '/report', function () {
        $file = storage_path('architecture/report.html');
        if (!file_exists($file)) {
            abort(404, 'No report found. Run php artisan laradar:scan first.');
        }
        return response(file_get_contents($file), 200, ['Content-Type' => 'text/html']);
    })->name('laradar.report');

    // AI endpoints
    Route::post($path . '/ai/analyze',       [AIController::class, 'analyze'])->name('laradar.ai.analyze');
    Route::post($path . '/ai/chat',          [AIController::class, 'chat'])->name('laradar.ai.chat');
    Route::post($path . '/ai/documentation', [AIController::class, 'documentation'])->name('laradar.ai.documentation');
    Route::post($path . '/ai/report',        [AIController::class, 'report'])->name('laradar.ai.report');
    Route::get($path  . '/ai/job/{id}',      [AIController::class, 'jobStatus'])->name('laradar.ai.job.status');

    // Serve package static assets without requiring vendor:publish
    Route::get($path . '/assets/{filename}', function (string $filename) {
        $file = realpath(__DIR__ . '/../public/' . $filename);
        $base = realpath(__DIR__ . '/../public');
        if (!$file || !str_starts_with($file, $base) || !file_exists($file)) {
            abort(404);
        }
        $mime = match (pathinfo($filename, PATHINFO_EXTENSION)) {
            'svg'   => 'image/svg+xml',
            'ico'   => 'image/x-icon',
            'png'   => 'image/png',
            'css'   => 'text/css',
            default => 'application/octet-stream',
        };
        return response()->file($file, ['Content-Type' => $mime, 'Cache-Control' => 'public, max-age=86400']);
    })->name('laradar.asset')->where('filename', '[a-zA-Z0-9_\-\.]+');
});
