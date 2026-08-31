<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('accounting_vehicle_trips')) {

            Schema::create('accounting_vehicle_trips', function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | OPERACIÓN CONTABLE
                |--------------------------------------------------------------------------
                */

                $table->foreignId('accounting_transaction_id')
                    ->constrained('accounting_transactions')
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | CONFIGURACIÓN DE COSTEO UTILIZADA
                |--------------------------------------------------------------------------
                |
                | Permite saber con qué configuración histórica se calculó
                | este recorrido.
                |
                */

                $table->foreignId('vehicle_cost_configuration_id')
                    ->nullable()
                    ->constrained('vehicle_cost_configurations')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | DATOS DEL RECORRIDO
                |--------------------------------------------------------------------------
                */

                $table->date('trip_date');

                $table->string('concept', 180);

                $table->string('origin', 180)
                    ->nullable();

                $table->string('destination', 180)
                    ->nullable();

                $table->decimal('kilometers', 12, 2);

                /*
                |--------------------------------------------------------------------------
                | SNAPSHOT DEL COSTO
                |--------------------------------------------------------------------------
                |
                | Aunque después cambie el costo/km de la configuración,
                | este recorrido conserva el valor con el que fue calculado.
                |
                */

                $table->decimal('cost_per_km', 12, 6);

                $table->decimal('calculated_cost', 14, 2);

                /*
                |--------------------------------------------------------------------------
                | INFORMACIÓN ADICIONAL
                |--------------------------------------------------------------------------
                */

                $table->text('notes')
                    ->nullable();

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | ÍNDICES CORTOS
                |--------------------------------------------------------------------------
                */

                $table->index(
                    ['accounting_transaction_id', 'trip_date'],
                    'idx_vehicle_trip_transaction'
                );

                $table->index(
                    ['trip_date', 'is_active'],
                    'idx_vehicle_trip_date'
                );
            });
        }
    }

    public function down(): void
    {
        //
    }
};