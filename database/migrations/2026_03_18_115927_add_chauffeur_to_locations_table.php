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
        Schema::table('locations', function (Blueprint $table) {
            $table->boolean('avec_chauffeur')->default(false)->after('voiture_id');
            $table->foreignId('chauffeur_id')->nullable()->after('avec_chauffeur')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['chauffeur_id']);
            $table->dropColumn(['avec_chauffeur', 'chauffeur_id']);
        });
    }
};
