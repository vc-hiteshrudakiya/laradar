<?php

namespace Vcian\Laradar\AI\Providers;

class GroqProvider extends OpenAICompatibleProvider
{
    private const API_BASE = 'https://api.groq.com/openai/v1';

    protected function apiBase(): string { return self::API_BASE; }
    protected function name(): string    { return 'groq'; }
}
