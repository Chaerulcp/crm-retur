<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Exception;

class ClaudeClient
{
    private PendingRequest $client;
    private string $model;

    public function __construct()
    {
        $apiKey = config('services.anthropic.api_key');
        $this->model = config('services.anthropic.model');

        if (empty($apiKey)) {
            throw new Exception("Anthropic API Key is not set in environment or config.");
        }

        $this->client = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->baseUrl('https://api.anthropic.com/v1');
    }

    /**
     * Send a request to Claude Messages API
     */
    public function createMessage(string $systemPrompt, array $messages, int $maxTokens = 1024, ?string $modelOverride = null): array
    {
        $response = $this->client->post('/messages', [
            'model' => $modelOverride ?? $this->model,
            'max_tokens' => $maxTokens,
            'system' => $systemPrompt,
            'messages' => $messages,
        ]);

        if ($response->failed()) {
            throw new Exception('Claude API error: ' . $response->body());
        }

        return $response->json();
    }
}
