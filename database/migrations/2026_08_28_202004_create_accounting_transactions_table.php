<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_transactions', function (Blueprint $table) {
            $table->id();

            // Relaciones principales
            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            $table->foreignId('prospect_id')
                ->nullable()
                ->constrained('prospects')
                ->nullOnDelete();

            $table->foreignId('tramite_id')
                ->nullable()
                ->constrained('tramites')
                ->nullOnDelete();

            $table->foreignId('property_id')
                ->nullable()
                ->constrained('properties')
                ->nullOnDelete();

            // Tipo de operación
            $table->string('operation_type', 80);

            $table->string('description', 255)
                ->nullable();

            // Valores de referencia
            $table->decimal('published_price', 14, 2)
                ->nullable();

            $table->decimal('closing_price', 14, 2)
                ->nullable();

            // Corretaje negociable
            $table->decimal('brokerage_percentage', 8, 4)
                ->nullable();

            $table->decimal('brokerage_amount', 14, 2)
                ->nullable();

            // Servicios independientes
            $table->decimal('service_amount', 14, 2)
                ->nullable();

            // Totales contables
            $table->decimal('gross_income', 14, 2)
                ->default(0);

            $table->decimal('direct_expenses_total', 14, 2)
                ->default(0);

            $table->decimal('advisor_commissions_total', 14, 2)
                ->default(0);

            $table->decimal('general_expenses_prorated', 14, 2)
                ->default(0);

            $table->decimal('net_profit', 14, 2)
                ->default(0);

            // Estado de la operación
            $table->string('status', 50)
                ->default('Pendiente');

            // Trazabilidad
            $table->string('origin_module', 80)
                ->nullable();

            $table->string('source_type', 100)
                ->nullable();

            $table->unsignedBigInteger('source_id')
                ->nullable();

            // Fechas administrativas
            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('invoiced_at')
                ->nullable();

            $table->timestamp('closed_at')
                ->nullable();

            // Observaciones
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index(['status', 'operation_type']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_transactions');
    }
};