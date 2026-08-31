<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('property_images', 'position')) {
            Schema::table('property_images', function (Blueprint $table) {
                $table->integer('position')
                    ->default(0)
                    ->after('is_primary');
            });
        }
    }

    public function down(): void
    {
        // Se deja vacío porque la columna ya existía previamente
        // y no queremos eliminarla en un rollback.
    }
};