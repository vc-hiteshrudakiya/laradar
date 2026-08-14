<?php

namespace Hitesh\LaravelArchitectureDiscovery\Services;

use InvalidArgumentException;

class ReportExporter
{
    public function export(ArchitectureReport $report, string $format, string $path): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        match ($format) {
            'json'     => file_put_contents($path, $report->toJson()),
            'html'     => file_put_contents($path, $this->renderHtml($report)),
            'markdown' => file_put_contents($path, $this->renderMarkdown($report)),
            default    => throw new InvalidArgumentException("Unsupported format: {$format}. Supported: json, html, markdown"),
        };
    }

    private function renderHtml(ArchitectureReport $report): string
    {
        return view('architecture-discovery::report', [
            'data' => $report->getReport(),
        ])->render();
    }

    private function renderMarkdown(ArchitectureReport $report): string
    {
        $data = $report->getReport();
        $out  = [];

        // ── Header ────────────────────────────────────────────────
        $out[] = "# Architecture Report — {$data['project']['name']}";
        $out[] = '';
        $out[] = "> Generated: {$data['generated_at']}  ";
        $out[] = "> Laravel {$data['laravel_version']} · PHP {$data['php_version']} · laravel-architecture-discovery v{$data['package_version']}";
        $out[] = '';
        $out[] = '---';
        $out[] = '';

        // ── Architecture Score ────────────────────────────────────
        if (!empty($data['score'])) {
            $s = $data['score'];
            $out[] = '## Architecture Score';
            $out[] = '';
            $out[] = "**{$s['score']} / {$s['max']}** — {$s['grade']}";
            $out[] = '';
            foreach ($s['checks'] as $check) {
                $icon = match ($check['status']) { 'pass' => '✔', 'warn' => '⚠', default => '✘' };
                $note = $check['note'] ? " — *{$check['note']}*" : '';
                $out[] = "{$icon} {$check['label']}{$note}";
            }
            $out[] = '';
            $out[] = '---';
            $out[] = '';
        }

        // ── Summary ───────────────────────────────────────────────
        $out[] = '## Summary';
        $out[] = '';
        $out[] = '| Component | Count |';
        $out[] = '|-----------|------:|';
        $out[] = "| Models | {$data['summary']['models']} |";
        $out[] = "| Controllers | {$data['summary']['controllers']} |";
        $out[] = "| Routes | {$data['summary']['routes']} |";
        $out[] = "| Dependency edges | " . count($data['dependencies']['edges']) . " |";

        // Route analysis
        $rs = $data['route_summary'];
        if (!empty($rs['api_versions'])) {
            $out[] = "| API Versions | " . implode(', ', array_keys($rs['api_versions'])) . " |";
        }
        $out[] = "| Named Routes | {$rs['named_count']} / {$rs['total']} |";
        $out[] = '';

        // ── Dependency Graph (Mermaid — renders on GitHub) ────────
        $deps = $data['dependencies'];
        if (!empty($deps['edges'])) {
            $out[] = '---';
            $out[] = '';
            $out[] = '## Dependency Graph';
            $out[] = '';
            $out[] = '```mermaid';
            $out[] = 'flowchart TD';

            $layerStyle = [
                'controller' => ':::controller',
                'service'    => ':::service',
                'repository' => ':::repository',
                'model'      => ':::model',
            ];

            // Collect nodes indexed by name for style lookup
            $nodeMap = [];
            foreach ($deps['nodes'] as $node) {
                $nodeMap[$node['name']] = $node;
            }

            foreach ($deps['edges'] as $edge) {
                $fromStyle = $layerStyle[$nodeMap[$edge['from']]['layer'] ?? ''] ?? '';
                $toStyle   = $layerStyle[$nodeMap[$edge['to']]['layer'] ?? ''] ?? '';
                $out[] = "    {$edge['from']}{$fromStyle} --> {$edge['to']}{$toStyle}";
            }

            $out[] = '';
            $out[] = '    classDef controller fill:#dbeafe,stroke:#3b82f6,color:#1e3a8a';
            $out[] = '    classDef service    fill:#d1fae5,stroke:#10b981,color:#064e3b';
            $out[] = '    classDef repository fill:#fef3c7,stroke:#f59e0b,color:#78350f';
            $out[] = '    classDef model      fill:#ede9fe,stroke:#8b5cf6,color:#4c1d95';
            $out[] = '```';
            $out[] = '';
        }

        // ── Models ────────────────────────────────────────────────
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
                    $out[] = "| `{$rel['method']}` | `{$rel['type']}` | `{$related}` |";
                }
                $out[] = '';
            }
            $out[] = '---';
            $out[] = '';
        }

        // ── Controllers ───────────────────────────────────────────
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

        // ── Routes ────────────────────────────────────────────────
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
            $out[] = "| {$methods} | `{$route['uri']}` | {$ctrl} | {$action} | {$name} | {$mw} |";
        }
        $out[] = '';

        return implode("\n", $out);
    }
}
