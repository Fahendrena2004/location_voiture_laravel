<?php

use App\Models\User;
use App\Models\Client;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('name', 'fafana')->first();
$client = Client::find(1);

if ($user && $client) {
    $client->user_id = $user->id;
    $client->save();
    echo "Linked User ID {$user->id} (fafana) to Client ID {$client->id} (Rafano).\n";
} else {
    echo "User or Client not found. User found: " . ($user ? 'Yes' : 'No') . ", Client found: " . ($client ? 'Yes' : 'No') . "\n";
}
