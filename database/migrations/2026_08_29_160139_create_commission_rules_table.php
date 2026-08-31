<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('commission_configuration_id')
                ->constrained('commission_configurations')
                ->cascadeOnDelete();

            $table->string('code', 100);
            $table->string('name', 150);

            $table->enum('participation_type', [
                'capture',
                'sale',
                'capture_and_sale',
                'support',
                'closing',
                'other'
            ]);

            $table->enum('capture_origin', [
                'agency',
                'advisor',
                'any'
            ])->default('any');

            $table->decimal('percentage', 8, 4)->default(0);

            $table->enum('distribution_type', [
                'individual',
                'pool_equal',
                'pool_manual'
            ])->default('individual');

            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('priority')->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique([
                'commission_configuration_id',
                'code'
            ], 'commission_rules_configuration_code_unique');

            $table->index('participation_type');
            $table->index('capture_origin');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};