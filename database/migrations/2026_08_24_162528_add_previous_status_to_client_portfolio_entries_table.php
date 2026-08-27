<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_portfolio_entries', function (Blueprint $table) {

            $table->string('previous_status', 100)
                ->nullable()
                ->after('source_record_id');

        });
    }

    public function down(): void
    {
        Schema::table('client_portfolio_entries', function (Blueprint $table) {

            $table->dropColumn('previous_status');

        });
    }
};