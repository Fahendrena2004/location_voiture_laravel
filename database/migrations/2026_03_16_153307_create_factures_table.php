<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->unique()->constrained()->onDelete('cascade');
            $table->string('numero_facture')->unique();
            $table->date('date_facture');
            $table->decimal('montant_total', 10, 2);
            $table->enum('statut', ['payée', 'en attente', 'annulée'])->default('en attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
