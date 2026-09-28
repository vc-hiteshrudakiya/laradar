<?php

namespace Vcian\Laradar\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Vcian\Laradar\AI\AIManager;

class LaradarAIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300; // overridden by config in constructor — set LARADAR_AI_JOB_TIMEOUT in .env

    public function __construct(
        private string $jobId,
        private string $type,    // 'analyze' | 'documentation' | 'report'
        private array  $report,
        private array  $payload, // { type } for documentation, empty for analyze|report
    ) {
        $this->timeout = (int) config('laradar.ai.job_timeout', 300);
    }

    public function handle(AIManager $ai): void
    {
        Cache::put("laradar_ai_{$this->jobId}", ['status' => 'running'], 1800);

        try {
            $result = match ($this->type) {
                'analyze' => $ai->analyze($this->report)->toArray(),
                'documentation' => [
                    'content'  => $ai->generateDocumentation($this->report, $this->payload['type']),
                    'type'     => $this->payload['type'],
                    'filename' => ucfirst($this->payload['type']) . '.md',
                ],
                'report' => $ai->generateReport($this->report),
                default => throw new \InvalidArgumentException("Unknown job type: {$this->type}"),
            };

            Cache::put("laradar_ai_{$this->jobId}", ['status' => 'done', 'result' => $result], 1800);
        } catch (\Throwable $e) {
            Cache::put("laradar_ai_{$this->jobId}", ['status' => 'failed', 'error' => $e->getMessage()], 1800);
        }
    }

    public function failed(\Throwable $e): void
    {
        Cache::put("laradar_ai_{$this->jobId}", ['status' => 'failed', 'error' => $e->getMessage()], 1800);
    }
}
