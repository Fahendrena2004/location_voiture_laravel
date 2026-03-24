<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Dropping the old constraint and adding the new one with 'en attente'
            DB::statement('ALTER TABLE locations DROP CONSTRAINT IF EXISTS locations_statut_check');
            DB::statement("ALTER TABLE locations ADD CONSTRAINT locations_statut_check CHECK (statut::text = ANY (ARRAY['en attente'::text, 'en cours'::text, 'terminée'::text, 'annulée'::text]))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE locations DROP CONSTRAINT IF EXISTS locations_statut_check');
            DB::statement("ALTER TABLE locations ADD CONSTRAINT locations_statut_check CHECK (statut::text = ANY (ARRAY['en cours'::text, 'terminée'::text, 'annulée'::text]))");
        }
    }
};
