<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Guarda el motivo detallado de la cancelación
            $table->text('cancellation_reason')->nullable()->after('status');
            
            // Registra si el cliente fue derivado a la cartera general (1/0)
            $table->boolean('rescued_to_portfolio')->default(false)->after('cancellation_reason');
            
            // Opcional: Fecha y hora exacta de la cancelación
            $table->timestamp('cancelled_at')->nullable()->after('rescued_to_portfolio');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['cancellation_reason', 'rescued_to_portfolio', 'cancelled_at']);
        });
    }
};