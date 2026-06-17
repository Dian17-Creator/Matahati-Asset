<?php

namespace App\Jobs;

use App\Models\AssetReminder;
use App\Models\ErpToken;
use App\Models\muser;
use Google\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
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

        // Validasi: Cek apakah hari ini adalah tanggal pengingat yang valid di database.
        // Jika user mengubah tanggal reminder sebelum hari H, job lama yang ada di queue
        // akan diabaikan karena tanggalnya tidak cocok dengan hari ini.
        $today = \Carbon\Carbon::today()->toDateString();
        $reminderDate = \Carbon\Carbon::parse($reminder->reminder_date)->toDateString();
        if ($today !== $reminderDate) {
            Log::info("Job Asset Reminder ID: {$reminder->id} diabaikan karena tanggal reminder di DB ({$reminderDate}) tidak cocok dengan hari ini ({$today}).");
            return;
        }

        Log::info(
            "Memulai job notifikasi untuk Asset Reminder ID: "
                . $reminder->id
        );

        // 1. Ambil Nama Aset
        $assetName = '-';
        if ($reminder->asset_type === 'QR') {
            $assetName = $reminder->assetQr->cnama ?? optional(optional($reminder->assetQr)->subKategori)->cnama ?? '-';
        } elseif ($reminder->asset_type === 'NOQR') {
            $assetName = optional($reminder->assetNoQr)->cnama ?? '-';
        }

        // 2. Siapkan Judul & Isi Notifikasi
        $title = "Pengingat Pemeliharaan Aset";
        $body = "Aset {$assetName} memerlukan tindakan. Catatan: " . ($reminder->note ?? '-');

        // 3. Ambil Token Penerima (Semua Admin & Super Admin)
        $adminIds = muser::where('fadmin', 1)
            ->orWhere('fsuper', 1)
            ->pluck('nid')
            ->toArray();

        $tokens = ErpToken::whereIn('nuserid', $adminIds)
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            Log::info("Tidak ada token device Admin yang ditemukan untuk mengirim notifikasi.");
            return;
        }

        try {
            // 4. Ambil OAuth2 Token
            $accessToken = $this->getAccessToken();

            $successCount = 0;
            $failCount = 0;

            $isProduction = config('app.env') === 'production';
            $httpClient = Http::asJson();
            if (!$isProduction) {
                $httpClient = $httpClient->withoutVerifying();
            }

            // 5. Kirim Notifikasi via FCM HTTP v1 API
            foreach ($tokens as $token) {
                $response = $httpClient->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->post(
                    'https://fcm.googleapis.com/v1/projects/absensi-2b01e/messages:send',
                    [
                        "message" => [
                            "token" => $token,
                            "notification" => [
                                "title" => $title,
                                "body"  => $body
                            ],
                            "android" => [
                                "priority" => "high"
                            ],
                            "data" => [
                                "click_action" => "OPEN_ACTIVITY",
                                "reminder_id" => (string)$reminder->id
                            ]
                        ]
                    ]
                );

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failCount++;
                    Log::warning("Gagal mengirim FCM token ke {$token}. Response: " . json_encode($response->json()));
                }
            }

            Log::info("Pengiriman notifikasi selesai. Berhasil: {$successCount}, Gagal: {$failCount}");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi FCM: " . $e->getMessage());
        }
    }

    /**
     * Ambil OAuth2 Access Token dari Firebase Service Account
     */
    private function getAccessToken()
    {
        $client = new Client();

        $guzzleClient = null;
        // Lewati verifikasi SSL jika di lokal Windows untuk menghindari cURL error 60
        if (config('app.env') !== 'production') {
            $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
            $client->setHttpClient($guzzleClient);
        }

        $client->setAuthConfig(storage_path('app/firebase-service-account.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $guzzleClient
            ? $client->fetchAccessTokenWithAssertion($guzzleClient)
            : $client->fetchAccessTokenWithAssertion();

        return $token['access_token'];
    }
}
