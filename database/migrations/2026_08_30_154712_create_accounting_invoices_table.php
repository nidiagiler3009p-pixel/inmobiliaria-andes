<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_invoices', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | OPERACIÓN DE ORIGEN
            |--------------------------------------------------------------------------
            */
            $table->foreignId('accounting_transaction_id')
                ->unique()
                ->constrained('accounting_transactions')
                ->cascadeOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | DATOS DEL CLIENTE - SNAPSHOT HISTÓRICO
            |--------------------------------------------------------------------------
            */
            $table->string('identification_type', 30)
                ->default('cedula');

            $table->string('identification_number', 30)
                ->nullable();

            $table->string('customer_name', 255);

            $table->string('business_name', 255)
                ->nullable();

            $table->string('billing_address', 500)
                ->nullable();

            $table->string('phone', 50)
                ->nullable();

            $table->string('email', 255)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DATOS DEL COMPROBANTE
            |--------------------------------------------------------------------------
            */
            $table->string('document_type', 30)
                ->default('factura');

            $table->string('invoice_number', 50)
                ->nullable()
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | VALORES
            |--------------------------------------------------------------------------
            */
            $table->decimal('subtotal', 12, 2)
                ->default(0);

            $table->decimal('tax_percentage', 8, 4)
                ->default(0);

            $table->decimal('tax_amount', 12, 2)
                ->default(0);

            $table->decimal('total', 12, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | ESTADO
            |--------------------------------------------------------------------------
            */
            $table->string('status', 30)
                ->default('Borrador');

            $table->timestamp('issued_at')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_invoices');
    }
};