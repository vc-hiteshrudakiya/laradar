<?php

namespace Vcian\Laradar\Analyzers;

class RouteAnalyzer
{
    public function __construct(private string $appNamespace) {}

    public function analyze(): array
    {
        $includeVendor = config('laradar.include_vendor_routes', false);

        $items  = [];
        $errors = [];

        foreach (app('router')->getRoutes() as $route) {
            try {
                $action = $route->getActionName();

                if (!$includeVendor && $this->isVendorRoute($action)) {
                    continue;
                }

                [$class, $method] = $this->parseAction($action);

                $middleware = array_values($route->gatherMiddleware());

                $items[] = [
                    'uri'        => $route->uri(),
                    'methods'    => $route->methods(),
                    'controller' => [
                        'class'  => $class,
                        'method' => $method,
                    ],
                    'name'       => $route->getName(),
                    'middleware' => $middleware,
                    'prefix'     => $this->extractPrefix($route),
                    'domain'     => $route->getDomain() ?: null,
                    'rate_limit' => $this->extractRateLimit($middleware),
                ];
            } catch (\Throwable $e) {
                $errors[] = [
                    'file'    => 'route: ' . $route->uri(),
                    'message' => $e->getMessage(),
                ];
            }
        }

        return ['items' => $items, 'errors' => $errors];
    }

    private function extractPrefix($route): ?string
    {
        $action = $route->getAction();
        $prefix = $action['prefix'] ?? null;
        $prefix = $prefix ? trim($prefix, '/') : null;
        return $prefix ?: null;
    }

    private function extractRateLimit(array $middleware): ?string
    {
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'throttle:')) {
                return substr($mw, 9);
            }
            if ($mw === 'throttle') {
                return 'default';
            }
        }
        return null;
    }

    private function isVendorRoute(string $action): bool
    {
        if ($action === 'Closure') {
            return false;
        }

        $class = str_contains($action, '@') ? explode('@', $action)[0] : $action;

        // Use detected app namespace — works for any project, not just App\
        return !str_starts_with($class, $this->appNamespace . '\\');
    }

    private function parseAction(string $action): array
    {
        if (str_contains($action, '@')) {
            [$class, $method] = explode('@', $action);
            return [$class, $method];
        }

        if ($action === 'Closure') {
            return ['Closure', null];
        }

        return [$action, '__invoke'];
    }
}
