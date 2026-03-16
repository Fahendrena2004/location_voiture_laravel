<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VoitureController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\FactureController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::resource('clients', ClientController::class);
    Route::resource('voitures', VoitureController::class);
    Route::resource('entretiens', EntretienController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('paiements', PaiementController::class);
    Route::resource('factures', FactureController::class);
});

require __DIR__ . '/settings.php';
