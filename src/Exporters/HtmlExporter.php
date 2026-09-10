<?php

namespace Vcian\Laradar\Exporters;

use Vcian\Laradar\Services\ArchitectureReport;

class HtmlExporter
{
    public function render(ArchitectureReport $report): string
    {
        return view('laradar::report', [
            'data' => $report->getReport(),
        ])->render();
    }
}
