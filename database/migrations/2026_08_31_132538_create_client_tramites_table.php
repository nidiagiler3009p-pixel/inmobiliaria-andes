<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_tramites', function (Blueprint $table) {
            $table->id();

            // Cliente que ya ingresó a Clientes / Trámites
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            // Prospecto central relacionado
            $table->unsignedBigInteger('prospect_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | ORIGEN DEL REGISTRO
            |--------------------------------------------------------------------------
            |
            | appointment = Gestión de Citas
            | tramite     = Citas Integrales
            | cartera     = Cartera
            |
            */
            $table->string('source_type', 50)->nullable();

            // ID del registro original.
            // Solo referencia histórica; Clientes NO modificará ese registro.
            $table->unsignedBigInteger('source_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | ESTADO PROPIO DE CLIENTES / TRÁMITES
            |--------------------------------------------------------------------------
            */
            $table->string('status', 50)->default('Pendiente');

            // Fechas del proceso
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            // Resultado final
            $table->string('result', 50)->nullable();

            // Motivo cuando termina sin éxito / observaciones del proceso
            $table->text('notes')->nullable();

            // Usuario que realizó la transmutación
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */
            $table->index('prospect_id');
            $table->index(['source_type', 'source_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_tramites');
    }
};