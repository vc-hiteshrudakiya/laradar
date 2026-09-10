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
              'packages', 'ai', 'chat', 'aidocs'] as $section) {
        Route::get($path . '/' . $section, [DashboardController::class, 'section'])
            ->name('laradar.' . $section)
            ->defaults('section', $section);
    }

    // Model detail page
    Route::get($path . '/models/{model}', [DashboardController::class, 'modelDetail'])
        ->name('laradar.model.detail')
        ->where('model', '[a-zA-Z0-9_]+');

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
