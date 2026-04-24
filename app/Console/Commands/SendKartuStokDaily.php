<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KartuStokService;

class SendKartuStokDaily extends Command
{
    protected $signature = 'app:send-kartu-stok-daily';
    protected $description = 'Kirim kartu stok harian';

    public function handle()
    {
        app(KartuStokService::class)->run();

        $this->info('Kartu stok harian terkirim!');
    }
}
