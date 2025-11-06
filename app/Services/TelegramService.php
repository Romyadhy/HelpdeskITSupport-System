<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected string $token;

    protected ?string $defaultChatId;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $this->defaultChatId = config('services.telegram.chat_id', env('TELEGRAM_DEFAULT_CHAT_ID'));
    }

    public function sendMessage(string $text, ?string $chatId = null): bool
    {
        $chatId = $chatId ?? $this->defaultChatId;

        if (! $this->token || ! $chatId) {
            return false;
        }
        try {
            $response = Http::withoutVerifying()->post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);
            \Log::info('Telegram Response: ' . $response->body());
        } catch (\Exception $e) {
            \Log::error('Telegram Exception: ' . $e->getMessage());
        }

        return $response->successful();
    }
}
