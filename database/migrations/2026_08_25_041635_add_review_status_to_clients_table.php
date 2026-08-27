<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {

            $table->enum(
                'review_status',
                [
                    'Pendiente',
                    'Confirmado'
                ]
            )
            ->default('Pendiente')
            ->after('status');

        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('review_status');
        });
    }
};