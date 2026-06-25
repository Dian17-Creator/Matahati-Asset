<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \App\Models\muser::all();
foreach ($users as $u) {
    echo "ID: {$u->nid} | Name: {$u->cname} | Email: {$u->cemail} | Admin: {$u->fadmin} | Super: {$u->fsuper}\n";
}
