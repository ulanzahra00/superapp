<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private string $apiKey;
    private string $groupId;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.fonnte.api_key');
        $this->groupId = config('services.fonnte.group_id');
        $this->baseUrl = 'https://api.fonnte.com';
    }

    /**
     * Send a message to the WhatsApp group via Fonnte API.
     */
    public function sendToGroup(string $message): bool
    {
        if (empty($this->apiKey) || empty($this->groupId)) {
            Log::warning('FonnteService: API key or Group ID not configured.');
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post($this->baseUrl . '/send', [
            'target' => $this->groupId,
            'message' => $message,
        ]);

        if ($response->successful()) {
            Log::info('FonnteService: Message sent successfully to group.', [
                'response' => $response->json(),
            ]);
            return true;
        }

        Log::error('FonnteService: Failed to send message.', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return false;
    }
}