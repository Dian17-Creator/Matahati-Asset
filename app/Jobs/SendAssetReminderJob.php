<?php

namespace App\Jobs;

use App\Models\AssetReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAssetReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reminderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $reminderId)
    {
        $this->reminderId = $reminderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reminder = AssetReminder::find($this->reminderId);

        if (!$reminder) {
            Log::warning("Asset Reminder ID: {$this->reminderId} tidak ditemukan saat menjalankan job.");
            return;
        }

        Log::info(
            "Memulai job notifikasi untuk Asset Reminder ID: "
                . $reminder->id
        );

        Log::info(
            "Job notifikasi selesai untuk reminder ID: "
                . $reminder->id
        );
    }
}
