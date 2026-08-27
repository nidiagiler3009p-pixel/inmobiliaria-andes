<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('last_name', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('identification', 30)->nullable();
            $table->string('status', 50)->default('Prospecto');
            $table->string('first_source', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('phone');
            $table->index('email');
            $table->index('identification');
        });
    }

    public function down(): void {
        Schema::dropIfExists('prospects');
    }
};