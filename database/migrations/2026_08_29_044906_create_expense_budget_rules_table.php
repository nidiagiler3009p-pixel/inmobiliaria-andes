<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expense_budget_rules')) {

            Schema::create('expense_budget_rules', function (Blueprint $table) {
                $table->id();

                $table->foreignId('expense_category_id')
                    ->constrained('expense_categories')
                    ->cascadeOnDelete();

                $table->foreignId('expense_subcategory_id')
                    ->nullable()
                    ->constrained('expense_subcategories')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | MÉTODO DE CÁLCULO
                |--------------------------------------------------------------------------
                |
                | Manual
                | Porcentaje
                | Valor fijo
                | Por km
                | Prorrateado
                | Sin asignación
                |
                */

                $table->string('calculation_method', 50)
                    ->default('Manual');

                /*
                |--------------------------------------------------------------------------
                | VALORES CONFIGURABLES
                |--------------------------------------------------------------------------
                */

                $table->decimal('percentage', 8, 4)
                    ->nullable();

                $table->decimal('fixed_amount', 14, 2)
                    ->nullable();

                $table->decimal('cost_per_km', 14, 4)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | BASE DEL PORCENTAJE
                |--------------------------------------------------------------------------
                |
                | Ingreso bruto
                | Comisión inmobiliaria
                | Precio de cierre
                | Otra
                |
                */

                $table->string('percentage_base', 80)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | VIGENCIA
                |--------------------------------------------------------------------------
                |
                | Esto permite que el porcentaje cambie sin modificar
                | períodos anteriores.
                |
                */

                $table->date('effective_from');

                $table->date('effective_until')
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

                $table->index([
                    'expense_category_id',
                    'effective_from'
                ]);

                $table->index([
                    'expense_subcategory_id',
                    'effective_from'
                ]);
            });
        }
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Vacío intencionalmente.
        | Evitamos eliminar reglas históricas accidentalmente.
        |--------------------------------------------------------------------------
        */
    }
};