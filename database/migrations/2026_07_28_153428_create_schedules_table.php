<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');

            $table->enum('event_type', ['Cita', 'Asignación'])->default('Cita');
            $table->string('title'); // Ej: "Visita: Prop. 12" o "Preparar Documentos"
            
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();

            $table->enum('status', ['Pendiente', 'Confirmada', 'Completada', 'Cancelada'])->default('Pendiente');
            $table->boolean('alert_sent')->default(false); // Para notificaciones flotantes

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};

