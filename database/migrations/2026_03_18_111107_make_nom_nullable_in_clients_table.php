<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     * Rend la colonne 'nom' nullable car elle n'est pas requise
     * pour les clients de type 'association' (qui utilisent 'raison_sociale').
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('nom')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('nom')->nullable(false)->change();
        });
    }
};
