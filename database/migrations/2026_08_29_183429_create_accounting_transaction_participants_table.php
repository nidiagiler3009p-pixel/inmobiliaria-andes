<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_transaction_participants', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('accounting_transaction_id');

            $table->unsignedBigInteger('user_id');

            $table->enum('participation_type', [
                'capture',
                'sale',
                'support',
                'closing',
                'other',
            ]);

            $table->decimal('distribution_percentage', 8, 4)
                ->nullable();

            $table->enum('source', [
                'property',
                'manual',
            ])->default('property');

            $table->boolean('is_active')
                ->default(true);

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
             * Claves foráneas con nombres cortos
             * para evitar el límite de MySQL.
             */
            $table->foreign(
                'accounting_transaction_id',
                'atp_transaction_fk'
            )
                ->references('id')
                ->on('accounting_transactions')
                ->cascadeOnDelete();

            $table->foreign(
                'user_id',
                'atp_user_fk'
            )
                ->references('id')
                ->on('users');

            $table->index(
                [
                    'accounting_transaction_id',
                    'participation_type',
                    'is_active',
                ],
                'atp_transaction_type_active_idx'
            );

            $table->index(
                [
                    'accounting_transaction_id',
                    'user_id',
                ],
                'atp_transaction_user_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'accounting_transaction_participants'
        );
    }
};