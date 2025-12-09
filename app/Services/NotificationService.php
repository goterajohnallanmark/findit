<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a match notification to n8n webhook.
     *
     * @param string $email Recipient email address
     * @param array|object $item Item details (name, description, id, link)
     * @param array $extra Optional extra info (e.g., matched_date, finder_name)
     * @return array { success: bool, status: int|null, body: mixed }
     */
    public function notifyMatch(string $email, $item, array $extra = []): array
    {
        $webhookUrl = config('services.n8n_webhook_url') ?? 'http://localhost:5678/webhook/lost-found';

        // Normalize item to array
        $itemPayload = is_array($item) ? $item : (method_exists($item, 'toArray') ? $item->toArray() : (array) $item);

        $payload = [
            'email' => trim($email),
            'item'  => $itemPayload,
            'matched_date' => $extra['matched_date'] ?? now()->toDateTimeString(),
            'finder_name' => $extra['finder_name'] ?? 'Unknown',
        ];
        
        // Log the payload for debugging
        Log::info('Sending n8n notification', ['payload' => $payload]);

        try {
            $response = Http::asJson()->post($webhookUrl, $payload);

            $result = [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'body'    => $response->json() ?? $response->body(),
            ];

            if (!$response->successful()) {
                Log::error('n8n webhook notification failed', [
                    'url' => $webhookUrl,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload,
                ]);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('n8n webhook exception', [
                'url' => $webhookUrl,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status'  => null,
                'body'    => $e->getMessage(),
            ];
        }
    }
}
