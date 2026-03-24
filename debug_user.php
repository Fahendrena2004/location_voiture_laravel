<?php

use App\Models\User;
use App\Models\Client;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$allClients = Client::all();
foreach ($allClients as $client) {
    echo "Client: ID={$client->id}, Nom={$client->nom}, UserID=" . ($client->user_id ?? 'NULL') . "\n";
}
