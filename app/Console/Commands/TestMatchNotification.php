<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMatchNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test Lost & Found match notification to n8n webhook';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->ask('Recipient email', 'user@example.com');

        $item = [
            'name' => 'Black Wallet',
            'description' => 'Leather wallet with three cards and a receipt',
            'id' => 12345,
            'link' => url('/matches/12345'),
        ];

        $extra = [
            'matched_date' => now()->toDateTimeString(),
            'finder_name' => 'Alex Finder',
        ];

        $service = app(\App\Services\NotificationService::class);
        $result = $service->notifyMatch($email, $item, $extra);

        $this->info('Webhook call completed');
        $this->line('Success: ' . ($result['success'] ? 'true' : 'false'));
        $this->line('Status: ' . ($result['status'] ?? 'null'));
        $this->line('Body: ' . (is_string($result['body']) ? $result['body'] : json_encode($result['body'])));

        return $result['success'] ? self::SUCCESS : self::FAILURE;
    }
}
