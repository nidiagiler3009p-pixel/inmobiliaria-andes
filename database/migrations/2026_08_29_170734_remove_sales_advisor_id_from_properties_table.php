<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('properties', 'sales_advisor_id')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropForeign(['sales_advisor_id']);
                $table->dropColumn('sales_advisor_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('properties', 'sales_advisor_id')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->foreignId('sales_advisor_id')
                    ->nullable()
                    ->after('capturing_advisor_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }
};