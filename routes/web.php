<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VoitureController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');



Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('chauffeurs', ChauffeurController::class);
        Route::resource('voitures', VoitureController::class)->except(['index', 'show']);
        Route::resource('entretiens', EntretienController::class);
        Route::resource('comptes', \App\Http\Controllers\ComptePaiementController::class);

        Route::post('locations/{location}/approve', [LocationController::class, 'approve'])->name('locations.approve');
        Route::post('locations/{location}/reject', [LocationController::class, 'reject'])->name('locations.reject');
    });

    Route::get('voitures', [VoitureController::class, 'index'])->name('voitures.index');
    Route::get('voitures/{voiture}', [VoitureController::class, 'show'])->name('voitures.show');

    // Shared routes (with controller level filtering)
    Route::get('locations/available-cars', [LocationController::class, 'getAvailableCars'])->name('locations.available_cars');
    Route::resource('locations', LocationController::class);
    Route::resource('paiements', PaiementController::class);
    Route::resource('factures', FactureController::class);
    Route::get('factures/{facture}/pdf', [FactureController::class, 'downloadPdf'])->name('factures.pdf');
    Route::get('search', [SearchController::class, 'search'])->name('search');

    Route::post(
        'currency-switch',
        function (Illuminate\Http\Request $request) {
            $currency = $request->input('currency', 'EUR');
            if (in_array($currency, ['EUR', 'MGA'])) {
                session(['currency' => $currency]);
            }
            return back();
        }
    )->name('currency.switch');
});

require __DIR__ . '/settings.php';
