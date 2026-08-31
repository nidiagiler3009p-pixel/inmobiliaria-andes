<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo crea la columna si todavía no existe.
        if (!Schema::hasColumn('properties', 'price_dropped')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->boolean('price_dropped')
                    ->default(false)
                    ->after('price');
            });
        }
    }

    public function down(): void
    {
        // Solo elimina la columna si existe.
        if (Schema::hasColumn('properties', 'price_dropped')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('price_dropped');
            });
        }
    }
};