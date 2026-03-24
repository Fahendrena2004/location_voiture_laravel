<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE paiements DROP CONSTRAINT paiements_mode_paiement_check;');
            DB::statement("ALTER TABLE paiements ADD CONSTRAINT paiements_mode_paiement_check CHECK (mode_paiement::text = ANY (ARRAY['espèces'::character varying, 'carte'::character varying, 'virement'::character varying, 'bancaire'::character varying, 'mobile_money'::character varying]::text[]));");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE paiements DROP CONSTRAINT paiements_mode_paiement_check;');
            DB::statement("ALTER TABLE paiements ADD CONSTRAINT paiements_mode_paiement_check CHECK (mode_paiement::text = ANY (ARRAY['espèces'::character varying, 'carte'::character varying, 'virement'::character varying]::text[]));");
        }
    }
};
