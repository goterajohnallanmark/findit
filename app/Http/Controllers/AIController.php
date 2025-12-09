<?php

namespace App\Http\Controllers;

use App\Models\ItemMatch;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    public static function generateEmbedding(string $text): array
    {
        $text = trim($text);

        if ($text === '') {
            return [];
        }

        $baseUrl = config('services.ollama.url', env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'));
        $endpoint = rtrim($baseUrl, '/') . '/api/embed';

        try {
            $response = Http::timeout(30)->post($endpoint, [
                'model' => 'nomic-embed-text',
                'input' => $text,
            ]);

            if ($response->successful()) {
                return $response->json('embeddings.0', []);
            }

            Log::error('AI embedding request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return [];
        } catch (RequestException $exception) {
            Log::error('AI embedding request failed', ['exception' => $exception->getMessage()]);
            return [];
        } catch (\Exception $exception) {
            Log::error('AI embedding request failed', ['exception' => $exception->getMessage()]);
            return [];
        }
    }

    public static function cosineSimilarity(array $a, array $b): float
    {
        if (empty($a) || empty($b) || count($a) !== count($b)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $index => $value) {
            $dot += $value * ($b[$index] ?? 0);
            $normA += $value * $value;
            $normB += ($b[$index] ?? 0) * ($b[$index] ?? 0);
        }

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    public static function dispatchMatchWebhooks(ItemMatch $match)
    {
        $lostItem = $match->lostItem;
        $foundItem = $match->foundItem;

        if (! $lostItem || ! $foundItem) {
            return;
        }

        $payload = [
            'lost_user_email' => isset($lostItem->user) ? $lostItem->user->email : null,
            'found_user_email' => isset($foundItem->user) ? $foundItem->user->email : null,
            'lost_item' => [
                'id' => $lostItem->id,
                'title' => $lostItem->title,
                'description' => $lostItem->description,
                'category' => $lostItem->category,
                'location' => $lostItem->location,
                'lost_date' => isset($lostItem->lost_date) ? date('Y-m-d', strtotime($lostItem->lost_date)) : null,
                'image_url' => $lostItem->image_url,
            ],
            'found_item' => [
                'id' => $foundItem->id,
                'title' => $foundItem->title,
                'description' => $foundItem->description,
                'category' => $foundItem->category,
                'location' => $foundItem->location,
                'found_date' => isset($foundItem->found_date) ? date('Y-m-d', strtotime($foundItem->found_date)) : null,
                'image_url' => $foundItem->image_url,
            ],
            'similarity' => $match->similarity_score,
        ];

        static::postWebhook(config('services.n8n.match_webhook', env('N8N_MATCH_WEBHOOK')), $payload);
        static::postWebhook(config('services.n8n.notify_webhook', env('N8N_NOTIFY_FINDER_WEBHOOK')), $payload);
    }

    protected static function postWebhook(?string $url, array $payload)
    {
        if (empty($url)) {
            return;
        }

        try {
            Http::post($url, $payload);
        } catch (RequestException $exception) {
            Log::warning('n8n webhook failed', ['url' => $url, 'exception' => $exception]);
        }
    }
}
