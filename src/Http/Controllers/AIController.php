<?php

namespace Vcian\Laradar\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Vcian\Laradar\AI\AIManager;
use Vcian\Laradar\Jobs\LaradarAIJob;
use Vcian\Laradar\Laradar;

class AIController extends Controller
{
    private const DOC_TYPES = ['architecture', 'models', 'controllers', 'routes', 'services', 'modules'];

    public function __construct()
    {
        $perMinute = config('laradar.ai.rate_limit', 30);
        if ($perMinute > 0) {
            $this->middleware("throttle:{$perMinute},1")->only(['chat', 'analyze']);
        }
    }

    public function analyze(Request $request, Laradar $discovery, AIManager $ai): JsonResponse
    {
        if (!$ai->isEnabled()) {
            return response()->json([
                'error' => 'AI is disabled. Set AI_ENABLED=true and configure the API key for your chosen provider in your .env.',
            ], 503);
        }

        try {
            $reportData = $request->input('report');
            $report     = is_array($reportData) ? $reportData : $discovery->discover()->getReport();

            if (config('laradar.ai.async', false)) {
                $jobId = Str::uuid()->toString();
                Cache::put("laradar_ai_{$jobId}", ['status' => 'pending'], 1800);
                LaradarAIJob::dispatch($jobId, 'analyze', $report, [])
                    ->onQueue(config('laradar.ai.queue', 'default'));
                return response()->json(['job_id' => $jobId, 'status' => 'queued']);
            }

            $response = $ai->analyze($report);
            return response()->json($response->toArray());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function jobStatus(string $id): JsonResponse
    {
        $data = Cache::get("laradar_ai_{$id}");
        if (!$data) {
            return response()->json(['status' => 'not_found'], 404);
        }
        return response()->json($data);
    }

    public function chat(Request $request, AIManager $ai): JsonResponse
    {
        if (!$ai->isEnabled()) {
            return response()->json(['error' => 'AI is disabled.'], 503);
        }

        $message = trim($request->input('message', ''));
        if (empty($message)) {
            return response()->json(['error' => 'Message is required.'], 422);
        }
        $maxLen = config('laradar.ai.max_message_length', 5000);
        if (strlen($message) > $maxLen) {
            return response()->json(['error' => "Message too long. Maximum {$maxLen} characters allowed."], 422);
        }

        try {
            $context = $request->input('context', []);
            $reply   = $ai->chat($message, $context);
            return response()->json(['reply' => $reply]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function documentation(Request $request, Laradar $discovery, AIManager $ai): JsonResponse
    {
        if (!$ai->isEnabled()) {
            return response()->json(['error' => 'AI is disabled.'], 503);
        }

        $type = $request->input('type', 'architecture');
        if (!in_array($type, self::DOC_TYPES, true)) {
            return response()->json(['error' => 'Invalid doc type. Valid: ' . implode(', ', self::DOC_TYPES)], 422);
        }

        try {
            $reportData = $request->input('report');
            $report     = is_array($reportData) ? $reportData : $discovery->discover()->getReport();

            if (config('laradar.ai.async', false)) {
                $jobId = Str::uuid()->toString();
                Cache::put("laradar_ai_{$jobId}", ['status' => 'pending'], 1800);
                LaradarAIJob::dispatch($jobId, 'documentation', $report, ['type' => $type])
                    ->onQueue(config('laradar.ai.queue', 'default'));
                return response()->json(['job_id' => $jobId, 'status' => 'queued', 'type' => $type]);
            }

            $content = $ai->generateDocumentation($report, $type);
            return response()->json(['content' => $content, 'type' => $type, 'filename' => ucfirst($type) . '.md']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
