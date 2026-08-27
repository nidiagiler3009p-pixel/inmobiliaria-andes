<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_portfolio_entries', function (Blueprint $table) {

            $table->string('source_type', 50)
                ->nullable()
                ->after('appointment_id');

            $table->unsignedBigInteger('source_record_id')
                ->nullable()
                ->after('source_type');

            $table->index(
                ['source_type', 'source_record_id'],
                'portfolio_source_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('client_portfolio_entries', function (Blueprint $table) {

            $table->dropIndex('portfolio_source_index');

            $table->dropColumn([
                'source_type',
                'source_record_id'
            ]);
        });
    }
};