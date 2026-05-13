<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MassetQr;
use App\Models\MassetNoQr;
use App\Models\AssetReminder;

echo "--- ASSET REMINDER SAMPLE ---\n";
$reminder = AssetReminder::first();
if ($reminder) {
    print_r($reminder->toArray());
} else {
    echo "No reminder found.\n";
}

echo "\n--- ACTIVE QR ASSETS ---\n";
$qr = MassetQr::where('cstatus', 'Aktif')->limit(3)->get();
foreach ($qr as $item) {
    echo "ID: {$item->nid}, QR: {$item->cqr}, Nama: {$item->cnama}, Status: {$item->cstatus}\n";
}

echo "\n--- ACTIVE NO QR ASSETS ---\n";
$noqr = MassetNoQr::where('cstatus', 'Aktif')->where('nqty', '>', 0)->limit(3)->get();
foreach ($noqr as $item) {
    echo "Code: {$item->ckode}, Nama: {$item->cnama}, Status: {$item->cstatus}, Qty: {$item->nqty}\n";
}
