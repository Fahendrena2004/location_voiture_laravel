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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('type')->default('personne')->after('id');
            $table->string('prenom')->nullable()->change();
            $table->date('date_naissance')->nullable()->change();
            $table->string('raison_sociale')->nullable()->after('prenom');
            $table->string('cin', 20)->nullable()->after('date_naissance');
            $table->string('nif', 20)->nullable()->after('cin');
            $table->string('stat', 20)->nullable()->after('nif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('prenom')->nullable(false)->change();
            $table->date('date_naissance')->nullable(false)->change();
            $table->dropColumn(['type', 'raison_sociale', 'cin', 'nif', 'stat']);
        });
    }
};
