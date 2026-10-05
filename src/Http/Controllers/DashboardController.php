<?php

namespace Vcian\Laradar\Http\Controllers;

use Illuminate\Routing\Controller;
use Vcian\Laradar\Laradar;

class DashboardController extends Controller
{
    private function notFoundResponse(string $type, string $name, array $data = []): mixed
    {
        $section = $type;
        $view = view('laradar::sections.not-found', compact('type', 'name', 'section', 'data'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '', 404);
        }
        return response($view->render(), 404);
    }

    private function scanFailedResponse(\Throwable $e): mixed
    {
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

    private function render(string $section, Laradar $discovery)
    {
        try {
            $data = $discovery->discover()->getReport();

            return view('laradar::layouts.laradar', compact('data', 'section'));
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
    }

    public function modelDetail(Laradar $discovery, string $model)
    {
        try {
            $data      = $discovery->discover()->getReport();
            $index     = null;
            $modelData = null;
            foreach ($data['models'] ?? [] as $i => $m) {
                if (strtolower($m['name']) === strtolower($model)) {
                    $index     = $i;
                    $modelData = $m;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }

        if (!$modelData) {
            return $this->notFoundResponse('models', $model, $data);
        }

        $model   = $modelData;
        $section = 'models';
        $view = view('laradar::sections.model-detail', compact('data', 'section', 'model', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    private const SECTIONS = [
        'overview', 'models', 'controllers', 'routes', 'migrations', 'jobs', 'events',
        'services', 'repositories', 'observers', 'policies', 'modules', 'middleware',
        'packages', 'ai', 'chat', 'aidocs', 'guide',
    ];

    public function section(Laradar $discovery): mixed
    {
        $section = request()->route('section');
        if (!in_array($section, self::SECTIONS, true)) {
            abort(404);
        }
        return $this->render($section, $discovery);
    }

    public function controllerDetail(Laradar $discovery, string $controller)
    {
        try {
            $data           = $discovery->discover()->getReport();
            $index          = null;
            $controllerData = null;
            foreach ($data['controllers'] ?? [] as $i => $c) {
                if (strtolower($c['name']) === strtolower($controller)) {
                    $index          = $i;
                    $controllerData = $c;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }

        if (!$controllerData) {
            return $this->notFoundResponse('controllers', $controller, $data);
        }

        $section = 'controllers';
        $view = view('laradar::sections.controller-detail', compact('data', 'section', 'controllerData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function migrationDetail(Laradar $discovery, string $migration)
    {
        try {
            $data          = $discovery->discover()->getReport();
            $migrationData = null;
            $index         = null;
            foreach ($data['migrations'] ?? [] as $i => $m) {
                if ($m['filename'] === $migration) {
                    $migrationData = $m;
                    $index         = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }

        if (!$migrationData) {
            return $this->notFoundResponse('migrations', $migration, $data);
        }

        $section = 'migrations';
        $view = view('laradar::sections.migration-detail', compact('data', 'section', 'migrationData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function routeDetail(Laradar $discovery, string $route)
    {
        try {
            $data      = $discovery->discover()->getReport();
            $routeData = null;
            $index     = null;

            // Try by route name first
            foreach ($data['routes'] ?? [] as $i => $r) {
                if (($r['name'] ?? '') !== '' && ($r['name'] ?? '') === $route) {
                    $routeData = $r;
                    $index     = $i;
                    break;
                }
            }

            // Fallback: numeric index for unnamed routes
            if ($routeData === null && ctype_digit($route)) {
                $index     = (int) $route;
                $routeData = $data['routes'][$index] ?? null;
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }

        if ($routeData === null) {
            return $this->notFoundResponse('routes', $route, $data);
        }

        $section = 'routes';
        $view = view('laradar::sections.route-detail', compact('data', 'section', 'routeData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function serviceDetail(Laradar $discovery, string $service)
    {
        try {
            $data        = $discovery->discover()->getReport();
            $serviceData = null;
            $index       = null;
            foreach ($data['services'] ?? [] as $i => $s) {
                if (strtolower($s['name']) === strtolower($service)) {
                    $serviceData = $s;
                    $index       = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if (!$serviceData) {
            return $this->notFoundResponse('services', $service, $data);
        }
        $section = 'services';
        $view = view('laradar::sections.service-detail', compact('data', 'section', 'serviceData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function repositoryDetail(Laradar $discovery, string $repository)
    {
        try {
            $data           = $discovery->discover()->getReport();
            $repositoryData = null;
            $index          = null;
            foreach ($data['repositories'] ?? [] as $i => $r) {
                if (strtolower($r['name']) === strtolower($repository)) {
                    $repositoryData = $r;
                    $index          = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if (!$repositoryData) {
            return $this->notFoundResponse('repositories', $repository, $data);
        }
        $section = 'repositories';
        $view = view('laradar::sections.repository-detail', compact('data', 'section', 'repositoryData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function jobDetail(Laradar $discovery, string $job)
    {
        try {
            $data    = $discovery->discover()->getReport();
            $jobData = null;
            $index   = null;
            foreach ($data['jobs'] ?? [] as $i => $j) {
                if (strtolower($j['name']) === strtolower($job)) {
                    $jobData = $j;
                    $index   = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if (!$jobData) {
            return $this->notFoundResponse('jobs', $job, $data);
        }
        $section = 'jobs';
        $view = view('laradar::sections.job-detail', compact('data', 'section', 'jobData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function eventDetail(Laradar $discovery, string $event)
    {
        try {
            $data      = $discovery->discover()->getReport();
            $eventData = null;
            $index     = null;
            foreach ($data['events'] ?? [] as $i => $e) {
                if (strtolower($e['name']) === strtolower($event)) {
                    $eventData = $e;
                    $index     = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if (!$eventData) {
            return $this->notFoundResponse('events', $event, $data);
        }
        $section = 'events';
        $view = view('laradar::sections.event-detail', compact('data', 'section', 'eventData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function observerDetail(Laradar $discovery, string $observer)
    {
        try {
            $data         = $discovery->discover()->getReport();
            $observerData = null;
            $index        = null;
            foreach ($data['observers'] ?? [] as $i => $o) {
                if (strtolower($o['name']) === strtolower($observer)) {
                    $observerData = $o;
                    $index        = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if (!$observerData) {
            return $this->notFoundResponse('observers', $observer, $data);
        }
        $section = 'observers';
        $view = view('laradar::sections.observer-detail', compact('data', 'section', 'observerData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function policyDetail(Laradar $discovery, string $policy)
    {
        try {
            $data       = $discovery->discover()->getReport();
            $policyData = null;
            $index      = null;
            foreach ($data['policies'] ?? [] as $i => $p) {
                if (strtolower($p['name']) === strtolower($policy)) {
                    $policyData = $p;
                    $index      = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if (!$policyData) {
            return $this->notFoundResponse('policies', $policy, $data);
        }
        $section = 'policies';
        $view = view('laradar::sections.policy-detail', compact('data', 'section', 'policyData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function moduleDetail(Laradar $discovery, string $module)
    {
        try {
            $data       = $discovery->discover()->getReport();
            $moduleData = null;
            $index      = null;
            foreach ($data['modules'] ?? [] as $i => $m) {
                if (strtolower($m['name']) === strtolower($module)) {
                    $moduleData = $m;
                    $index      = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if (!$moduleData) {
            return $this->notFoundResponse('modules', $module, $data);
        }
        $section = 'modules';
        $view = view('laradar::sections.module-detail', compact('data', 'section', 'moduleData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function middlewareDetail(Laradar $discovery, string $mw)
    {
        try {
            $data       = $discovery->discover()->getReport();
            $mwUsage    = $data['route_summary']['middleware_usage'] ?? [];
            $routeCount = $mwUsage[$mw] ?? null;
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if ($routeCount === null) {
            return $this->notFoundResponse('middleware', $mw, $data);
        }
        $section        = 'middleware';
        $middlewareName = $mw;
        $view = view('laradar::sections.middleware-detail', compact('data', 'section', 'middlewareName', 'routeCount'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }

    public function packageDetail(Laradar $discovery, string $package)
    {
        try {
            $data        = $discovery->discover()->getReport();
            $packageData = null;
            $index       = null;
            foreach ($data['packages'] ?? [] as $i => $p) {
                if (($p['key'] ?? $p['name']) === $package || strtolower($p['name']) === strtolower($package)) {
                    $packageData = $p;
                    $index       = $i;
                    break;
                }
            }
        } catch (\Throwable $e) {
            return $this->scanFailedResponse($e);
        }
        if (!$packageData && ctype_digit($package)) {
            $index       = (int) $package;
            $packageData = $data['packages'][$index] ?? null;
        }
        if (!$packageData) {
            return $this->notFoundResponse('packages', $package, $data);
        }
        $section = 'packages';
        $view = view('laradar::sections.package-detail', compact('data', 'section', 'packageData', 'index'));
        if (request()->header('X-Partial') === '1') {
            return response($view->renderSections()['content'] ?? '');
        }
        return $view;
    }
}
