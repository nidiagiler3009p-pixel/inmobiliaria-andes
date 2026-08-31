<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('accounting_expenses', 'expense_category_id')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->foreignId('expense_category_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('expense_categories')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'accounting_transaction_id')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->foreignId('accounting_transaction_id')
                    ->nullable()
                    ->after('expense_category_id')
                    ->constrained('accounting_transactions')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'expense_type')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->string('expense_type', 30)
                    ->default('General')
                    ->after('expense_category');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'expense_date')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->date('expense_date')
                    ->nullable()
                    ->after('amount');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'provider')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->string('provider', 150)
                    ->nullable()
                    ->after('expense_date');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'document_number')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->string('document_number', 100)
                    ->nullable()
                    ->after('provider');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'document_path')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->string('document_path', 255)
                    ->nullable()
                    ->after('document_number');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'payment_status')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->string('payment_status', 50)
                    ->default('Pendiente')
                    ->after('document_path');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'payment_method')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->string('payment_method', 80)
                    ->nullable()
                    ->after('payment_status');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'payment_reference')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->string('payment_reference', 150)
                    ->nullable()
                    ->after('payment_method');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'paid_at')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->timestamp('paid_at')
                    ->nullable()
                    ->after('payment_reference');
            });
        }

        if (!Schema::hasColumn('accounting_expenses', 'notes')) {
            Schema::table('accounting_expenses', function (Blueprint $table) {
                $table->text('notes')
                    ->nullable()
                    ->after('paid_at');
            });
        }
    }

    public function down(): void
    {
        // Se deja vacío para proteger la estructura y los datos
        // existentes de accounting_expenses.
    }
};