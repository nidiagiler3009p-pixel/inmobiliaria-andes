<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expense_subcategories')) {

            Schema::create('expense_subcategories', function (Blueprint $table) {
                $table->id();

                $table->foreignId('expense_category_id')
                    ->constrained('expense_categories')
                    ->cascadeOnDelete();

                $table->string('name', 150);

                $table->string('code', 80)
                    ->nullable()
                    ->unique();

                $table->text('description')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | PRESUPUESTO
                |--------------------------------------------------------------------------
                |
                | Ninguno       = sin presupuesto automático
                | Porcentaje    = porcentaje sobre ingreso
                | Valor fijo    = monto presupuestado fijo
                | Por km        = usado especialmente para vehículo
                | Prorrateado   = se reparte entre operaciones
                |
                */

                $table->string('budget_method', 50)
                    ->default('Ninguno');

                $table->decimal('budget_percentage', 8, 4)
                    ->nullable();

                $table->decimal('fixed_budget_amount', 14, 2)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | CONTROL
                |--------------------------------------------------------------------------
                */

                $table->boolean('is_budgeted')
                    ->default(false);

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();

                $table->index([
                    'expense_category_id',
                    'is_active'
                ]);
            });
        }
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Vacío intencionalmente.
        | Evitamos eliminar datos contables accidentalmente.
        |--------------------------------------------------------------------------
        */
    }
};