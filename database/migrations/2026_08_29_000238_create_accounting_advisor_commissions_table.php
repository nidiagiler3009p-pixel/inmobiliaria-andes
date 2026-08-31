<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('accounting_advisor_commissions', 'accounting_transaction_id')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->foreignId('accounting_transaction_id')
                    ->after('id')
                    ->constrained('accounting_transactions')
                    ->cascadeOnDelete();
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'user_id')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->after('accounting_transaction_id')
                    ->constrained('users')
                    ->restrictOnDelete();
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'role_in_transaction')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->string('role_in_transaction', 80)
                    ->after('user_id');
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'percentage')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->decimal('percentage', 8, 4)
                    ->nullable()
                    ->after('role_in_transaction');
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'calculation_base')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->decimal('calculation_base', 14, 2)
                    ->nullable()
                    ->after('percentage');
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'commission_amount')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->decimal('commission_amount', 14, 2)
                    ->default(0)
                    ->after('calculation_base');
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'status')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->string('status', 50)
                    ->default('Pendiente')
                    ->after('commission_amount');
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'paid_at')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->timestamp('paid_at')
                    ->nullable()
                    ->after('status');
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'payment_method')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->string('payment_method', 80)
                    ->nullable()
                    ->after('paid_at');
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'payment_reference')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->string('payment_reference', 150)
                    ->nullable()
                    ->after('payment_method');
            });
        }

        if (!Schema::hasColumn('accounting_advisor_commissions', 'notes')) {
            Schema::table('accounting_advisor_commissions', function (Blueprint $table) {
                $table->text('notes')
                    ->nullable()
                    ->after('payment_reference');
            });
        }
    }

    public function down(): void
    {
        // Se deja vacío para no eliminar la tabla ni sus columnas,
        // ya que la tabla fue creada previamente por otra migración.
    }
};