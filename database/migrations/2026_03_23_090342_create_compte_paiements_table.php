<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compte_paiements', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'bancaire' ou 'mobile_money'
            $table->string('nom'); // Ex: BNI, MVola, Orange Money...
            $table->string('details', 255); // Ex: IBAN ou Numéro de téléphone
            $table->boolean('actif')->default(true); // Permet  à l'admin de désactiver un mode
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compte_paiements');
    }
};
