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

    public $reminder;

    /**
     * Create a new job instance.
     */
    public function __construct(AssetReminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info(
            "Memulai job notifikasi untuk Asset Reminder ID: "
                . $this->reminder->id
        );

        Log::info(
            "Job notifikasi selesai untuk reminder ID: "
                . $this->reminder->id
        );
    }
}
