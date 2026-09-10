<?php

namespace Vcian\Laradar\Services;

use InvalidArgumentException;
use Vcian\Laradar\Exporters\HtmlExporter;
use Vcian\Laradar\Exporters\MarkdownExporter;
class ReportExporter
{
    public function export(ArchitectureReport $report, string $format, string $path): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $content = match ($format) {
            'json'     => $report->toJson(),
            'html'     => $this->renderHtml($report),
            'markdown' => (new MarkdownExporter)->render($report),
            default    => throw new InvalidArgumentException(
                "Unsupported format: {$format}. Supported: json, html, markdown"
            ),
        };

        file_put_contents($path, $content);
    }

    public function renderHtml(ArchitectureReport $report): string
    {
        return (new HtmlExporter)->render($report);
    }
}
