<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\AssetReminder;
use Illuminate\Support\Facades\Log;

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
        Log::info("Memulai job notifikasi untuk Asset Reminder ID: " . $this->reminder->id);

        // TODO: Implementasikan logika untuk mengirim push notifikasi ke aplikasi Android (misalnya menggunakan Firebase Cloud Messaging)
        // Contoh:
        /*
        $tokens = \App\Models\DeviceToken::pluck('fcm_token')->toArray();
        $message = [
            'title' => 'Pengingat Asset',
            'body' => 'Ada pengingat untuk asset dengan tipe: ' . $this->reminder->asset_type,
        ];
        // Kirim request FCM di sini
        */

        Log::info("Job notifikasi untuk Asset Reminder ID: " . $this->reminder->id . " selesai dieksekusi.");
    }
}
