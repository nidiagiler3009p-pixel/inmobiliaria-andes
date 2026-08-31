<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('appointments_tracking', 'source_channel')) {
            Schema::table('appointments_tracking', function (Blueprint $table) {
                $table->string('source_channel')
                    ->nullable()
                    ->after('priority');
            });
        }

        if (!Schema::hasColumn('appointments_tracking', 'cancellation_reason')) {
            Schema::table('appointments_tracking', function (Blueprint $table) {
                $table->text('cancellation_reason')
                    ->nullable()
                    ->after('status');
            });
        }

        if (!Schema::hasColumn('appointments_tracking', 'rescued_to_portfolio')) {
            Schema::table('appointments_tracking', function (Blueprint $table) {
                $table->boolean('rescued_to_portfolio')
                    ->default(false)
                    ->after('cancellation_reason');
            });
        }

        if (!Schema::hasColumn('appointments_tracking', 'cancelled_at')) {
            Schema::table('appointments_tracking', function (Blueprint $table) {
                $table->timestamp('cancelled_at')
                    ->nullable()
                    ->after('rescued_to_portfolio');
            });
        }
    }

    public function down(): void
    {
        // Se deja vacío porque estos campos ya existían en la base
        // antes de registrar esta migración.
    }
};