<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('location_voiture', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->foreignId('voiture_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing data
        $locations = DB::table('locations')->whereNotNull('voiture_id')->get();
        foreach ($locations as $location) {
            DB::table('location_voiture')->insert([
                'location_id' => $location->id,
                'voiture_id' => $location->voiture_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop voiture_id constraint and column
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['voiture_id']);
            $table->dropColumn('voiture_id');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->foreignId('voiture_id')->nullable()->constrained()->onDelete('cascade');
        });

        $pivots = DB::table('location_voiture')->get();
        foreach ($pivots as $pivot) {
            // fallback
            DB::table('locations')->where('id', $pivot->location_id)->update(['voiture_id' => $pivot->voiture_id]);
        }

        Schema::dropIfExists('location_voiture');
    }
};
