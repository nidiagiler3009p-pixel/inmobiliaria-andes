<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_configurations', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();

            $table->boolean('is_active')->default(true);

            $table->enum('default_sales_distribution', [
                'equal',
                'manual'
            ])->default('equal');

            $table->boolean('allow_manual_distribution')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('effective_from');
            $table->index('effective_to');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_configurations');
    }
};