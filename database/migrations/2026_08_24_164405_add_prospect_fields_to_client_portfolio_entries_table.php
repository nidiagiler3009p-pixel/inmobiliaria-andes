<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('client_portfolio_entries', 'prospect_name')) {
            Schema::table('client_portfolio_entries', function (Blueprint $table) {
                $table->string('prospect_name', 150)
                    ->nullable()
                    ->after('previous_status');
            });
        }

        if (!Schema::hasColumn('client_portfolio_entries', 'prospect_last_name')) {
            Schema::table('client_portfolio_entries', function (Blueprint $table) {
                $table->string('prospect_last_name', 150)
                    ->nullable()
                    ->after('prospect_name');
            });
        }

        if (!Schema::hasColumn('client_portfolio_entries', 'prospect_phone')) {
            Schema::table('client_portfolio_entries', function (Blueprint $table) {
                $table->string('prospect_phone', 30)
                    ->nullable()
                    ->after('prospect_last_name');
            });
        }

        if (!Schema::hasColumn('client_portfolio_entries', 'prospect_email')) {
            Schema::table('client_portfolio_entries', function (Blueprint $table) {
                $table->string('prospect_email', 255)
                    ->nullable()
                    ->after('prospect_phone');
            });
        }
    }

    public function down(): void
    {
        // Se deja vacío porque estos campos ya existían
        // antes de que esta migración quedara registrada.
    }
};