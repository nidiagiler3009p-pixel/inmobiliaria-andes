<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('property_id')->nullable();
            $table->timestamp('registration_date')->nullable();
            $table->dateTime('appointment_date');
            $table->boolean('is_notified')->default(false);
            $table->string('location_reference')->nullable();
            $table->string('status')->default('Pendiente');
            $table->text('notes')->nullable();
            $table->string('type')->default('visita');
            $table->string('priority')->default('normal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments_tracking');
    }
};