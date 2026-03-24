<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Facture;

$factures = Facture::all(['id', 'numero_facture', 'date_facture']);
echo "COUNT: " . $factures->count() . PHP_EOL;
foreach ($factures as $f) {
    echo "ID: {$f->id} | NO: {$f->numero_facture} | DATE: {$f->date_facture}" . PHP_EOL;
}
