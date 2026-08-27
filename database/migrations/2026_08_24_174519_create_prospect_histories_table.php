<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prospect_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained('prospects')->cascadeOnDelete();
            $table->string('event_type', 100);
            $table->string('source_type', 50)->nullable();
            $table->unsignedBigInteger('source_record_id')->nullable();
            $table->string('previous_status', 100)->nullable();
            $table->string('new_status', 100)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['source_type', 'source_record_id']);
            $table->index('event_type');
        });
    }

    public function down(): void {
        Schema::dropIfExists('prospect_histories');
    }
};