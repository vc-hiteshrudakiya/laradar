<?php

namespace Vcian\Laradar\AI\Contracts;

use Vcian\Laradar\AI\DTO\AIAnalysisResponse;

interface AIProvider
{
    /**
     * Full architecture review — returns structured insights.
     */
    public function analyze(array $architectureData): AIAnalysisResponse;

    /**
     * Single-turn chat with architecture context.
     */
    public function chat(string $message, array $context = []): string;

    /**
     * Generate prose documentation for the architecture.
     * $type: architecture | models | controllers | routes | services | modules
     */
    public function generateDocumentation(array $architectureData, string $type = 'architecture'): string;

    /**
     * Generate documentation for multiple types in parallel.
     * Returns [ 'architecture' => '...', 'models' => '...', ... ]
     */
    public function generateDocumentationBatch(array $architectureData, array $types): array;

    /**
     * Alias for analyze() — kept explicit for semantic clarity.
     */
    public function reviewArchitecture(array $architectureData): AIAnalysisResponse;
}
