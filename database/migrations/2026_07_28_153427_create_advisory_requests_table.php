<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advisory_requests', function (Blueprint $table) {
            $table->id();
            
            // 1. Plan Seleccionado
            $table->enum('plan_type', ['Gratis', 'Estándar', 'Total']);
            $table->foreignId('advisor_id')->nullable()->constrained('users')->onDelete('set null'); // Asesor de preferencia seleccionado

            // 2. Paso 1: Datos Personales
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('ciudad');
            $table->string('discovery_channel')->nullable(); // ¿Cómo nos conoció?

            // 3. Paso 2: Datos de la Propiedad (Resumen ingresado por el cliente)
            $table->string('property_type')->nullable();
            $table->string('property_location')->nullable();
            $table->decimal('estimated_price', 12, 2)->nullable();
            $table->text('property_details')->nullable();

            // 4. Paso 3 & 4: Preferencias y Control de Estado
            $table->text('preferences_notes')->nullable();
            $table->boolean('accepted_terms')->default(false);
            $table->enum('status', ['Pendiente', 'En Contacto', 'Asignado', 'Finalizado'])->default('Pendiente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisory_requests');
    }
};

