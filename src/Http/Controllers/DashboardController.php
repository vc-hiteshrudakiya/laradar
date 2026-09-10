<?php

namespace Vcian\Laradar\Http\Controllers;

use Illuminate\Routing\Controller;
use Vcian\Laradar\Laradar;

class DashboardController extends Controller
{
    private function render(string $section, Laradar $discovery)
    {
        try {
            $data = $discovery->discover()->getReport();

            return view('laradar::layouts.laradar', compact('data', 'section'));
        } catch (\Throwable $e) {
            return response(
                '<html><body style="font-family:sans-serif;padding:40px;background:#0f172a;color:#f1f5f9;">'
                . '<h2 style="color:#f87171;">Laradar — Scan Failed</h2>'
                . '<p style="color:#94a3b8;">The architecture scan encountered an error:</p>'
                . '<pre style="background:#1e293b;padding:16px;border-radius:8px;color:#fca5a5;font-size:13px;">' . htmlspecialchars($e->getMessage()) . '</pre>'
                . '<p style="color:#64748b;font-size:12px;">Check your <code>config/laradar.php</code> and ensure the package is installed correctly.</p>'
                . '</body></html>',
                500
            );
        }
    }

    public function modelDetail(Laradar $discovery, string $model)
    {
        try {
            $data      = $discovery->discover()->getReport();
            $index     = null;
            $modelData = null;
            foreach ($data['models'] as $i => $m) {
                if (strtolower($m['name']) === strtolower($model)) {
                    $index     = $i;
                    $modelData = $m;
                    break;
                }
            }
            if (!$modelData) abort(404);
            $model   = $modelData;
            $section = 'models';
            return view('laradar::sections.model-detail', compact('data', 'section', 'model', 'index'));
        } catch (\Throwable $e) {
            return response(
                '<html><body style="font-family:sans-serif;padding:40px;background:#0f172a;color:#f1f5f9;">'
                . '<h2 style="color:#f87171;">Laradar — Scan Failed</h2>'
                . '<pre style="background:#1e293b;padding:16px;border-radius:8px;color:#fca5a5;">' . htmlspecialchars($e->getMessage()) . '</pre>'
                . '</body></html>',
                500
            );
        }
    }

    private const SECTIONS = [
        'overview', 'models', 'controllers', 'routes', 'migrations', 'jobs', 'events',
        'services', 'repositories', 'observers', 'policies', 'modules', 'middleware',
        'packages', 'ai', 'chat', 'aidocs',
    ];

    public function section(Laradar $discovery): mixed
    {
        $section = request()->route('section');
        if (!in_array($section, self::SECTIONS, true)) {
            abort(404);
        }
        return $this->render($section, $discovery);
    }
}
