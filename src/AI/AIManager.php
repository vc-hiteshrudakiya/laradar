<?php

namespace Vcian\Laradar\AI;

use Vcian\Laradar\AI\Contracts\AIProvider;
use Vcian\Laradar\AI\DTO\AIAnalysisResponse;
use Vcian\Laradar\AI\Providers\AnthropicProvider;
use Vcian\Laradar\AI\Providers\GeminiProvider;
use Vcian\Laradar\AI\Providers\MistralProvider;
use Vcian\Laradar\AI\Providers\OllamaProvider;
use Vcian\Laradar\AI\Providers\OpenAIProvider;
use Vcian\Laradar\AI\Providers\OpenRouterProvider;
use RuntimeException;

class AIManager
{
    private ?AIProvider $resolvedProvider = null;

    /** Custom providers registered via extend(), keyed by name. */
    private array $customProviders = [];

    public function __construct(private readonly array $config) {}

    public function isEnabled(): bool
    {
        return (bool) ($this->config['enabled'] ?? false);
    }

    public function analyze(array $architectureData): AIAnalysisResponse
    {
        try {
            return $this->provider()->analyze($architectureData);
        } catch (\Throwable $e) {
            return $this->withFallback(fn($p) => $p->analyze($architectureData), $e);
        }
    }

    public function reviewArchitecture(array $architectureData): AIAnalysisResponse
    {
        try {
            return $this->provider()->reviewArchitecture($architectureData);
        } catch (\Throwable $e) {
            return $this->withFallback(fn($p) => $p->reviewArchitecture($architectureData), $e);
        }
    }

    public function chat(string $message, array $context = []): string
    {
        try {
            return $this->provider()->chat($message, $context);
        } catch (\Throwable $e) {
            return $this->withFallback(fn($p) => $p->chat($message, $context), $e);
        }
    }

    public function generateDocumentation(array $architectureData, string $type = 'architecture'): string
    {
        try {
            return $this->provider()->generateDocumentation($architectureData, $type);
        } catch (\Throwable $e) {
            return $this->withFallback(fn($p) => $p->generateDocumentation($architectureData, $type), $e);
        }
    }

    public function generateReport(array $architectureData): array
    {
        $docTypes = ['architecture', 'models', 'controllers', 'routes', 'services', 'modules'];

        $analysis = $this->analyze($architectureData)->toArray();

        try {
            $docs = $this->provider()->generateDocumentationBatch($architectureData, $docTypes);
        } catch (\Throwable $e) {
            // Fallback to sequential if batch fails for any reason
            $docs = [];
            foreach ($docTypes as $type) {
                $docs[$type] = $this->retryWithBackoff(fn() => $this->generateDocumentation($architectureData, $type));
            }
        }

        return ['analysis' => $analysis, 'docs' => $docs];
    }

    private function retryWithBackoff(callable $fn, int $maxRetries = 3): ?string
    {
        $delay = 10;
        for ($i = 0; $i <= $maxRetries; $i++) {
            try {
                return $fn();
            } catch (\Throwable $e) {
                $isRateLimit = str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota') || str_contains($e->getMessage(), 'rate');
                if ($i === $maxRetries || !$isRateLimit) return null;
                sleep($delay);
                $delay *= 2;
            }
        }
        return null;
    }

    private function withFallback(\Closure $call, \Throwable $original): mixed
    {
        $fallbackName = $this->config['fallback_provider'] ?? null;
        $primaryName  = $this->config['provider'] ?? null;

        if ($fallbackName && $fallbackName !== $primaryName) {
            try {
                return $call($this->makeProvider($fallbackName));
            } catch (\Throwable) {
                // fallback failed — surface the original primary error
            }
        }

        throw $original;
    }

    public function provider(): AIProvider
    {
        if ($this->resolvedProvider === null) {
            $provider = $this->config['provider'] ?? null;

            if (!$provider) {
                throw new RuntimeException(
                    'No AI provider could be detected. Add an API key to your .env (e.g. ANTHROPIC_API_KEY, OPENAI_API_KEY, GEMINI_API_KEY) and set AI_ENABLED=true.'
                );
            }

            $this->resolvedProvider = $this->makeProvider($provider);
        }

        return $this->resolvedProvider;
    }

    /**
     * Register a custom provider by name. If the registered name matches the
     * currently configured provider it is used immediately; otherwise it is
     * stored and will be resolved when provider() is next called with that name.
     */
    public function extend(string $name, AIProvider $provider): void
    {
        $this->customProviders[$name] = $provider;

        // If this name is the active provider, apply it right away.
        if (($this->config['provider'] ?? null) === $name) {
            $this->resolvedProvider = $provider;
        }
    }

    private function makeProvider(string $name): AIProvider
    {
        $name = strtolower($name);

        // Custom providers registered via extend() take priority.
        if (isset($this->customProviders[$name])) {
            return $this->customProviders[$name];
        }

        return match ($name) {
            'gemini'     => new GeminiProvider($this->config),
            'openai'     => new OpenAIProvider($this->config),
            'anthropic'  => new AnthropicProvider($this->config),
            'mistral'    => new MistralProvider($this->config),
            'ollama'     => new OllamaProvider($this->config),
            'openrouter' => new OpenRouterProvider($this->config),
            default      => throw new RuntimeException(
                "AI provider \"{$name}\" is not supported. Available: gemini, openai, anthropic, mistral, ollama, openrouter"
            ),
        };
    }
}
