<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments_tracking', function (Blueprint $table) {
            // Canal de captación (Web, Redes Sociales, WhatsApp, etc.)
            $table->string('source_channel')->nullable()->after('priority');
            
            // Campos de Cancelación
            $table->text('cancellation_reason')->nullable()->after('status');
            $table->boolean('rescued_to_portfolio')->default(false)->after('cancellation_reason');
            $table->timestamp('cancelled_at')->nullable()->after('rescued_to_portfolio');
        });
    }

    public function down(): void
    {
        Schema::table('appointments_tracking', function (Blueprint $table) {
            $table->dropColumn([
                'source_channel',
                'cancellation_reason',
                'rescued_to_portfolio',
                'cancelled_at'
            ]);
        });
    }
};