<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendReportReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-report-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService)
    {
        $today = Carbon::now()->format('d-m-Y');

        $message = "⏰ <b>Reminder Daily Report</b>\n\n"
            . "Tanggal: <b>{$today}</b>\n"
            . "Jangan lupa mengisi laporan harian.";

        $success = $telegramService->sendMessage($message);

        if ($success) {
            $this->info('Reminder berhasil dikirim.');
        } else {
            $this->error('Reminder GAGAL dikirim.');
        }

        return $success ? 0 : 1;
    }
}
