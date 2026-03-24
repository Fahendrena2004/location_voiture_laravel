<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$results = \Illuminate\Support\Facades\DB::select('SELECT id, numero_facture, date_facture FROM factures ORDER BY id ASC');
$output = "";
foreach ($results as $r) {
    $output .= "ID: " . $r->id . " | NUM: " . $r->numero_facture . " | DATE: " . $r->date_facture . PHP_EOL;
}
file_put_contents('debug_results_utf8.txt', $output);
