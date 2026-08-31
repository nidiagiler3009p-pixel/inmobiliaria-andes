<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_cost_configurations')) {

            Schema::create('vehicle_cost_configurations', function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | IDENTIFICACIÓN / PERÍODO
                |--------------------------------------------------------------------------
                */

                $table->string('name', 150)
                    ->default('Configuración vehículo');

                $table->date('effective_from');

                $table->date('effective_until')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | COMBUSTIBLE
                |--------------------------------------------------------------------------
                */

                $table->decimal('fuel_price_per_gallon', 12, 4)
                    ->nullable();

                $table->decimal('vehicle_efficiency_km_per_gallon', 12, 4)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | ACEITE
                |--------------------------------------------------------------------------
                */

                $table->decimal('oil_change_cost', 12, 2)
                    ->nullable();

                $table->decimal('oil_change_interval_km', 12, 2)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | NEUMÁTICOS
                |--------------------------------------------------------------------------
                */

                $table->decimal('tires_total_cost', 12, 2)
                    ->nullable();

                $table->decimal('tires_lifespan_km', 12, 2)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | MANTENIMIENTO
                |--------------------------------------------------------------------------
                */

                $table->decimal('maintenance_cost', 12, 2)
                    ->nullable();

                $table->decimal('maintenance_interval_km', 12, 2)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | COSTOS ANUALES
                |--------------------------------------------------------------------------
                */

                $table->decimal('annual_insurance_cost', 12, 2)
                    ->nullable();

                $table->decimal('annual_registration_cost', 12, 2)
                    ->nullable();

                $table->decimal('annual_other_vehicle_costs', 12, 2)
                    ->nullable();

                $table->decimal('estimated_annual_km', 12, 2)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | RESULTADOS CALCULADOS
                |--------------------------------------------------------------------------
                |
                | Los guardaremos como snapshot histórico.
                |
                */

                $table->decimal('fuel_cost_per_km', 12, 6)
                    ->nullable();

                $table->decimal('oil_cost_per_km', 12, 6)
                    ->nullable();

                $table->decimal('tires_cost_per_km', 12, 6)
                    ->nullable();

                $table->decimal('maintenance_cost_per_km', 12, 6)
                    ->nullable();

                $table->decimal('annual_costs_per_km', 12, 6)
                    ->nullable();

                $table->decimal('total_cost_per_km', 12, 6)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | CONTROL
                |--------------------------------------------------------------------------
                */

                $table->boolean('is_active')
                    ->default(true);

                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                $table->index(
                    ['effective_from', 'is_active'],
                    'idx_vehicle_cost_effective'
                );
            });
        }
    }

    public function down(): void
    {
        //
    }
};