<?php

namespace Vcian\Laradar\Http\Controllers;

use Illuminate\Routing\Controller;
use Vcian\Laradar\Laradar;

class DashboardController extends Controller
{
    public function __invoke(Laradar $discovery)
    {
        try {
            $report = $discovery->discover();
            $data   = $report->getReport();
            return view('laradar::dashboard', compact('data'));
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
}
