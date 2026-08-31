<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('properties', 'owner_name')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('owner_name')->nullable();
            });
        }

        if (!Schema::hasColumn('properties', 'owner_phone')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('owner_phone')->nullable();
            });
        }

        if (!Schema::hasColumn('properties', 'owner_dni')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('owner_dni')->nullable();
            });
        }

        if (!Schema::hasColumn('properties', 'owner_email')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('owner_email')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Se deja vacío porque estos campos ya existían previamente
        // y no queremos que un rollback elimine información histórica.
    }
};