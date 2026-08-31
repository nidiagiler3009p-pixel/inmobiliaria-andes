<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crear owner_name únicamente si todavía no existe.
        if (!Schema::hasColumn('properties', 'owner_name')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('owner_name')
                    ->nullable()
                    ->after('title');
            });
        }

        // Crear owner_phone únicamente si todavía no existe.
        if (!Schema::hasColumn('properties', 'owner_phone')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('owner_phone')
                    ->nullable()
                    ->after('owner_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('properties', 'owner_phone')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('owner_phone');
            });
        }

        if (Schema::hasColumn('properties', 'owner_name')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('owner_name');
            });
        }
    }
};