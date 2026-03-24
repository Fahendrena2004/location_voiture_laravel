<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('voitures', function (Blueprint $table) {
            $table->unsignedTinyInteger('nombre_places')->default(5)->after('couleur');
        });
    }

    public function down(): void
    {
        Schema::table('voitures', function (Blueprint $table) {
            $table->dropColumn('nombre_places');
        });
    }
};
