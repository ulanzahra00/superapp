<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private ?string $apiKey = null;
    private ?string $groupId = null;
    private string $baseUrl = 'https://api.fonnte.com';

    public function __construct()
    {
        $this->apiKey = config('services.fonnte.api_key');
        $this->groupId = config('services.fonnte.group_id');
    }

    /**
     * Send a message to the WhatsApp group via Fonnte API.
     */
    public function sendToGroup(string $message): bool
    {
        if (empty($this->apiKey) || empty($this->groupId)) {
            Log::warning('FonnteService: API key or Group ID not configured.', [
                'api_key_set' => !empty($this->apiKey),
                'group_id_set' => !empty($this->groupId),
            ]);
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

    /**
     * Send a message with a file attachment to the WhatsApp group via Fonnte API.
     * 
     * @param string $message The message text
     * @param string $filePath The full path to the file on local storage
     * @param string|null $filename Optional custom filename for the attachment
     */
    public function sendFileToGroup(string $message, string $filePath, ?string $filename = null): bool
    {
        if (empty($this->apiKey) || empty($this->groupId)) {
            Log::warning('FonnteService: API key or Group ID not configured.', [
                'api_key_set' => !empty($this->apiKey),
                'group_id_set' => !empty($this->groupId),
            ]);
            return false;
        }

        if (!file_exists($filePath)) {
            Log::error('FonnteService: File not found.', [
                'path' => $filePath,
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->attach(
                'file', file_get_contents($filePath), $filename ?? basename($filePath)
            )->post($this->baseUrl . '/send', [
                'target' => $this->groupId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('FonnteService: Message with file sent successfully to group.', [
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('FonnteService: Failed to send message with file.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('FonnteService: Exception sending file.', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
