<?php

namespace Vcian\Laradar\AI;

use Vcian\Laradar\AI\Contracts\AIProvider;
use Vcian\Laradar\AI\DTO\AIAnalysisResponse;
use Vcian\Laradar\AI\Providers\AnthropicProvider;
use Vcian\Laradar\AI\Providers\GeminiProvider;
use Vcian\Laradar\AI\Providers\GroqProvider;
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

    private function withFallback(\Closure $call, \Throwable $original): mixed
    {
        $fallbackName = $this->config['fallback_provider'] ?? null;
        $primaryName  = $this->config['provider'] ?? null;

        if ($fallbackName && $fallbackName !== $primaryName) {
            return $call($this->makeProvider($fallbackName));
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
        // Custom providers registered via extend() take priority.
        if (isset($this->customProviders[$name])) {
            return $this->customProviders[$name];
        }

        return match ($name) {
            'gemini'     => new GeminiProvider($this->config),
            'openai'     => new OpenAIProvider($this->config),
            'anthropic'  => new AnthropicProvider($this->config),
            'mistral'    => new MistralProvider($this->config),
            'groq'       => new GroqProvider($this->config),
            'ollama'     => new OllamaProvider($this->config),
            'openrouter' => new OpenRouterProvider($this->config),
            default      => throw new RuntimeException(
                "AI provider \"{$name}\" is not supported. Available: gemini, openai, anthropic, mistral, groq, ollama, openrouter"
            ),
        };
    }
}
