<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {

            $table->enum('capture_origin', [
                'agency',
                'advisor',
            ])
                ->default('agency')
                ->after('user_id');

            $table->foreignId('capturing_advisor_id')
                ->nullable()
                ->after('capture_origin')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('sales_advisor_id')
                ->nullable()
                ->after('capturing_advisor_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {

            $table->dropForeign([
                'capturing_advisor_id'
            ]);

            $table->dropForeign([
                'sales_advisor_id'
            ]);

            $table->dropColumn([
                'capture_origin',
                'capturing_advisor_id',
                'sales_advisor_id',
            ]);
        });
    }
};