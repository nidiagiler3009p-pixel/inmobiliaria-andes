<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments_tracking', function (Blueprint $table) {
            $table->foreignId('prospect_id')
                ->nullable()
                ->after('client_id')
                ->constrained('prospects')
                ->nullOnDelete();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('prospect_id')
                ->nullable()
                ->after('id')
                ->constrained('prospects')
                ->nullOnDelete();
        });

        Schema::table('advisory_requests', function (Blueprint $table) {
            $table->foreignId('prospect_id')
                ->nullable()
                ->after('id')
                ->constrained('prospects')
                ->nullOnDelete();
        });

        Schema::table('tramites', function (Blueprint $table) {
            $table->foreignId('prospect_id')
                ->nullable()
                ->after('id')
                ->constrained('prospects')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments_tracking', function (Blueprint $table) {
            $table->dropForeign(['prospect_id']);
            $table->dropColumn('prospect_id');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['prospect_id']);
            $table->dropColumn('prospect_id');
        });

        Schema::table('advisory_requests', function (Blueprint $table) {
            $table->dropForeign(['prospect_id']);
            $table->dropColumn('prospect_id');
        });

        Schema::table('tramites', function (Blueprint $table) {
            $table->dropForeign(['prospect_id']);
            $table->dropColumn('prospect_id');
        });
    }
};