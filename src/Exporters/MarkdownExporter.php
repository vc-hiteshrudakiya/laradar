<?php

namespace Vcian\Laradar\Exporters;

use Vcian\Laradar\Services\ArchitectureReport;

class MarkdownExporter
{
    public function render(ArchitectureReport $report): string
    {
        $data = $report->getReport();
        $out  = [];

        $out[] = "# Laradar Report — {$data['project']['name']}";
        $out[] = '';
        $out[] = "> Generated: {$data['generated_at']}  ";
        $out[] = "> Laravel {$data['laravel_version']} · PHP {$data['php_version']} · laradar v{$data['package_version']}";
        $out[] = '';
        $out[] = '---';
        $out[] = '';

        if (!empty($data['score'])) {
            $s = $data['score'];
            $out[] = '## Architecture Score';
            $out[] = '';
            $out[] = "**{$s['score']} / {$s['max']}** — {$s['grade']}";
            $out[] = '';
            foreach ($s['checks'] as $check) {
                $icon  = match ($check['status']) { 'pass' => '✔', 'warn' => '⚠', default => '✘' };
                $note  = $check['note'] ? " — *{$check['note']}*" : '';
                $out[] = "{$icon} {$check['label']}{$note}";
            }
            $out[] = '';
            $out[] = '---';
            $out[] = '';
        }

        $out[] = '## Summary';
        $out[] = '';
        $out[] = '| Component | Count |';
        $out[] = '|-----------|------:|';
        foreach ([
            'Models', 'Controllers', 'Routes', 'Jobs', 'Events',
            'Services', 'Repositories', 'Observers', 'Policies', 'Modules', 'Packages',
        ] as $label) {
            $key   = strtolower($label);
            $out[] = "| {$label} | {$data['summary'][$key]} |";
        }

        $rs      = $data['route_summary'];
        $mwCount = count($rs['middleware_usage'] ?? []);
        $out[]   = "| Named Routes | {$rs['named_count']} / {$rs['total']} |";
        $out[]   = "| Unique Middleware | {$mwCount} |";
        $out[]   = '';

        if (!empty($data['migrations'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Migrations';
            $out[] = '';
            $out[] = '| Date | Operation | Table | Columns | Foreign Keys |';
            $out[] = '|------|-----------|-------|--------:|-------------:|';
            foreach ($data['migrations'] as $mg) {
                $op    = strtoupper($mg['operation'] ?? 'unknown');
                $tbl   = $mg['table'] ?? '—';
                $cols  = count($mg['columns'] ?? []);
                $fks   = count($mg['foreign_keys'] ?? []);
                $date  = $mg['date'] ?? '—';
                $out[] = "| {$date} | {$op} | `{$tbl}` | {$cols} | {$fks} |";
            }
            $out[] = '';
        }

        $out[] = '---';
        $out[] = '';
        $out[] = '## Models';
        $out[] = '';
        foreach ($data['models'] as $model) {
            $out[] = "### {$model['name']}";
            $out[] = '';
            $out[] = "**Namespace:** `{$model['namespace']}`  ";
            $out[] = "**Table:** `{$model['table']}`  ";
            if (!empty($model['fillable'])) {
                $out[] = '**Fillable:** `' . implode('`, `', $model['fillable']) . '`';
            }
            if (!empty($model['hidden'])) {
                $out[] = '**Hidden:** `' . implode('`, `', $model['hidden']) . '`';
            }
            $out[] = '';
            if (!empty($model['relationships'])) {
                $out[] = '| Method | Type | Related |';
                $out[] = '|--------|------|---------|';
                foreach ($model['relationships'] as $rel) {
                    $related = class_basename($rel['related'] ?? '—');
                    $out[]   = "| `{$rel['method']}` | `{$rel['type']}` | `{$related}` |";
                }
                $out[] = '';
            }
            $out[] = '---';
            $out[] = '';
        }

        $out[] = '## Controllers';
        $out[] = '';
        foreach ($data['controllers'] as $ctrl) {
            $out[] = "### {$ctrl['name']}";
            $out[] = '';
            $out[] = "**Namespace:** `{$ctrl['namespace']}`  ";
            $out[] = "**Methods ({$ctrl['method_count']}):** " .
                     implode(', ', array_map(fn($m) => "`{$m}`", $ctrl['methods'] ?? []));
            $out[] = '';
            $out[] = '---';
            $out[] = '';
        }

        $out[] = '## Routes';
        $out[] = '';
        $out[] = '| Method | URI | Controller | Action | Name | Middleware |';
        $out[] = '|--------|-----|------------|--------|------|------------|';
        foreach ($data['routes'] as $route) {
            $methods = implode(',', array_filter($route['methods'] ?? [], fn($m) => $m !== 'HEAD'));
            $ctrl    = class_basename($route['controller']['class'] ?? '—');
            $action  = $route['controller']['method'] ?? '—';
            $name    = $route['name'] ?? '—';
            $mw      = implode(', ', $route['middleware'] ?? []);
            $out[]   = "| {$methods} | `{$route['uri']}` | {$ctrl} | {$action} | {$name} | {$mw} |";
        }
        $out[] = '';

        if (!empty($rs['middleware_usage'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Middleware Usage';
            $out[] = '';
            $out[] = '| Middleware | Count |';
            $out[] = '|------------|------:|';
            foreach ($rs['middleware_usage'] as $mw => $cnt) {
                $out[] = "| `{$mw}` | {$cnt} |";
            }
            $out[] = '';
        }

        if (!empty($data['jobs'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Jobs';
            $out[] = '';
            $out[] = '| Job | Namespace | Queue | Tries | Timeout |';
            $out[] = '|-----|-----------|-------|------:|--------:|';
            foreach ($data['jobs'] as $item) {
                $out[] = "| {$item['name']} | `{$item['namespace']}` | {$item['queue']} | {$item['tries']} | {$item['timeout']} |";
            }
            $out[] = '';
        }

        if (!empty($data['events'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Events';
            $out[] = '';
            $out[] = '| Event | Namespace | Listeners | Properties |';
            $out[] = '|-------|-----------|----------:|-----------:|';
            foreach ($data['events'] as $item) {
                $listeners = count($item['listeners'] ?? []);
                $props     = count($item['properties'] ?? []);
                $out[]     = "| {$item['name']} | `{$item['namespace']}` | {$listeners} | {$props} |";
            }
            $out[] = '';
        }

        if (!empty($data['services'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Services';
            $out[] = '';
            $out[] = '| Service | Namespace | Methods |';
            $out[] = '|---------|-----------|--------:|';
            foreach ($data['services'] as $item) {
                $mc    = count($item['methods'] ?? []);
                $out[] = "| {$item['name']} | `{$item['namespace']}` | {$mc} |";
            }
            $out[] = '';
        }

        if (!empty($data['repositories'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Repositories';
            $out[] = '';
            $out[] = '| Repository | Namespace | Methods |';
            $out[] = '|------------|-----------|--------:|';
            foreach ($data['repositories'] as $item) {
                $mc    = count($item['methods'] ?? []);
                $out[] = "| {$item['name']} | `{$item['namespace']}` | {$mc} |";
            }
            $out[] = '';
        }

        if (!empty($data['observers'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Observers';
            $out[] = '';
            $out[] = '| Observer | Namespace | Observed Model | Events |';
            $out[] = '|----------|-----------|----------------|--------|';
            foreach ($data['observers'] as $item) {
                $model  = class_basename($item['model'] ?? '—');
                $events = implode(', ', $item['events'] ?? []) ?: '—';
                $out[]  = "| {$item['name']} | `{$item['namespace']}` | {$model} | {$events} |";
            }
            $out[] = '';
        }

        if (!empty($data['policies'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Policies';
            $out[] = '';
            $out[] = '| Policy | Namespace | Model | Actions |';
            $out[] = '|--------|-----------|-------|---------|';
            foreach ($data['policies'] as $item) {
                $model   = class_basename($item['model'] ?? '—');
                $actions = implode(', ', $item['actions'] ?? []) ?: '—';
                $out[]   = "| {$item['name']} | `{$item['namespace']}` | {$model} | {$actions} |";
            }
            $out[] = '';
        }

        if (!empty($data['modules'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Modules';
            $out[] = '';
            $out[] = '| Module | Path | Routes |';
            $out[] = '|--------|------|-------:|';
            foreach ($data['modules'] as $item) {
                $out[] = "| {$item['name']} | `{$item['path']}` | {$item['routes']} |";
            }
            $out[] = '';
        }

        if (!empty($data['packages'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Packages';
            $out[] = '';
            $out[] = '| Package | Version | Type | Description |';
            $out[] = '|---------|---------|------|-------------|';
            foreach ($data['packages'] as $pkg) {
                $desc    = str_replace('|', '\\|', $pkg['description'] ?? '—');
                $version = $pkg['version'] ?? '—';
                $type    = $pkg['type'] ?? 'library';
                $out[]   = "| {$pkg['name']} | {$version} | {$type} | {$desc} |";
            }
            $out[] = '';
        }

        return implode("\n", $out);
    }
}
