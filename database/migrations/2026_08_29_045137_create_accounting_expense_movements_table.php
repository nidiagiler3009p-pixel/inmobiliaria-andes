<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CREAR TABLA SOLO SI NO EXISTE
        |--------------------------------------------------------------------------
        |
        | En local la tabla ya quedó creada porque MySQL alcanzó a crearla
        | antes de fallar con el nombre largo del índice.
        |
        */

        if (!Schema::hasTable('accounting_expense_movements')) {

            Schema::create('accounting_expense_movements', function (Blueprint $table) {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | CLASIFICACIÓN
                |--------------------------------------------------------------------------
                */

                $table->foreignId('expense_category_id')
                    ->constrained('expense_categories')
                    ->restrictOnDelete();

                $table->foreignId('expense_subcategory_id')
                    ->nullable()
                    ->constrained('expense_subcategories')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | OPERACIÓN RELACIONADA
                |--------------------------------------------------------------------------
                */

                $table->foreignId('accounting_transaction_id')
                    ->nullable()
                    ->constrained('accounting_transactions')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | DATOS DEL MOVIMIENTO
                |--------------------------------------------------------------------------
                */

                $table->string('concept', 255);

                $table->decimal('amount', 14, 2);

                $table->date('expense_date');

                $table->string('provider', 180)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | COMPROBANTE / FACTURA
                |--------------------------------------------------------------------------
                */

                $table->string('document_type', 80)
                    ->nullable();

                $table->string('document_number', 120)
                    ->nullable();

                $table->string('document_path', 500)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | ESTADO DE PAGO
                |--------------------------------------------------------------------------
                */

                $table->string('payment_status', 50)
                    ->default('Pendiente');

                $table->string('payment_method', 80)
                    ->nullable();

                $table->string('payment_reference', 150)
                    ->nullable();

                $table->timestamp('paid_at')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | CONTROL PRESUPUESTARIO
                |--------------------------------------------------------------------------
                */

                $table->boolean('was_budgeted')
                    ->default(true);

                $table->foreignId('budget_rule_id')
                    ->nullable()
                    ->constrained('expense_budget_rules')
                    ->nullOnDelete();

                $table->decimal('budgeted_amount', 14, 2)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | OBSERVACIONES
                |--------------------------------------------------------------------------
                */

                $table->text('notes')
                    ->nullable();

                $table->boolean('is_active')
                    ->default(true);

                $table->timestamps();
            });
        }

        /*
        |--------------------------------------------------------------------------
        | ÍNDICES CON NOMBRES CORTOS
        |--------------------------------------------------------------------------
        |
        | Se crean aparte para que esta migración también pueda reparar
        | la tabla que ya quedó creada parcialmente en local.
        |
        */

        if (
            Schema::hasTable('accounting_expense_movements') &&
            !Schema::hasIndex(
                'accounting_expense_movements',
                'idx_expense_cat_date'
            )
        ) {
            Schema::table('accounting_expense_movements', function (Blueprint $table) {
                $table->index(
                    ['expense_category_id', 'expense_date'],
                    'idx_expense_cat_date'
                );
            });
        }

        if (
            Schema::hasTable('accounting_expense_movements') &&
            !Schema::hasIndex(
                'accounting_expense_movements',
                'idx_expense_transaction_date'
            )
        ) {
            Schema::table('accounting_expense_movements', function (Blueprint $table) {
                $table->index(
                    ['accounting_transaction_id', 'expense_date'],
                    'idx_expense_transaction_date'
                );
            });
        }

        if (
            Schema::hasTable('accounting_expense_movements') &&
            !Schema::hasIndex(
                'accounting_expense_movements',
                'idx_expense_payment_date'
            )
        ) {
            Schema::table('accounting_expense_movements', function (Blueprint $table) {
                $table->index(
                    ['payment_status', 'expense_date'],
                    'idx_expense_payment_date'
                );
            });
        }
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Vacío intencionalmente.
        | No eliminamos movimientos contables accidentalmente.
        |--------------------------------------------------------------------------
        */
    }
};