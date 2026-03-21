<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chauffeur_location', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chauffeur_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing data
        $locations = DB::table('locations')->whereNotNull('chauffeur_id')->get();
        foreach ($locations as $location) {
            DB::table('chauffeur_location')->insert([
                'chauffeur_id' => $location->chauffeur_id,
                'location_id' => $location->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop columns
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['chauffeur_id']);
            $table->dropColumn('chauffeur_id');
            $table->dropColumn('avec_chauffeur');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->foreignId('chauffeur_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('avec_chauffeur')->default(false);
        });

        $pivots = DB::table('chauffeur_location')->get();
        foreach ($pivots as $pivot) {
            DB::table('locations')->where('id', $pivot->location_id)->update([
                'chauffeur_id' => $pivot->chauffeur_id,
                'avec_chauffeur' => true,
            ]);
        }

        Schema::dropIfExists('chauffeur_location');
    }
};
