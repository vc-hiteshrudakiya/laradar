<?php

namespace Vcian\Laradar\Support;

use Vcian\Laradar\Analyzers\ControllerAnalyzer;
use Vcian\Laradar\Analyzers\EventAnalyzer;
use Vcian\Laradar\Analyzers\JobAnalyzer;
use Vcian\Laradar\Analyzers\MigrationAnalyzer;
use Vcian\Laradar\Analyzers\ModelAnalyzer;
use Vcian\Laradar\Analyzers\ModuleAnalyzer;
use Vcian\Laradar\Analyzers\ObserverAnalyzer;
use Vcian\Laradar\Analyzers\PackageDetector;
use Vcian\Laradar\Analyzers\PolicyAnalyzer;
use Vcian\Laradar\Analyzers\RouteAnalyzer;
use Vcian\Laradar\Analyzers\ServiceAnalyzer;
use Vcian\Laradar\Services\ArchitectureScanner;

class ArchitectureScannerFactory
{
    public static function make(array $config): ArchitectureScanner
    {
        $scan         = $config['scan']          ?? [];
        $paths        = $config['paths']         ?? [];
        $appNamespace = $config['app_namespace'] ?? PathDetector::appNamespace();

        $analyzers = [];

        if ($scan['models'] ?? true) {
            $analyzers['models'] = new ModelAnalyzer(
                $paths['models'] ?? PathDetector::modelsPath()
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
                $paths['repositories'] ?? PathDetector::repositoriesPath(),
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
                $paths['modules'] ?? PathDetector::modulesPath()
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
    }
}
