<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tramites', function (Blueprint $table) {
            $table->id();

            // 1. Información Personal
            $table->string('first_name'); // Nombres completos
            $table->string('last_name');  // Apellidos completos
            $table->string('identification_card'); // Cédula de identidad (Ej: 1712345678)
            $table->string('email');      // Correo electrónico
            $table->string('phone');      // Teléfono / WhatsApp

            // 2. Información del Trámite
            $table->enum('tramite_type', [
                'Compra de propiedad',
                'Arriendo de propiedad',
                'Asesoría legal',
                'Avalúos y certificados',
                'Otros trámites'
            ]); // Tipo de trámite que deseas realizar
            
            $table->string('subject');    // Asunto / Referencia
            $table->text('message');      // Mensaje / Detalle de la consulta

            // 3. Preferencia de Contacto y Validación
            $table->enum('contact_preference', [
                'WhatsApp',
                'Llamada telefónica',
                'Correo electrónico'
            ])->default('WhatsApp'); // ¿Cómo prefieres que nos comuniquemos contigo?
            
            $table->boolean('accepted_privacy_policy')->default(false); // Acepto la política de privacidad

            // 4. Control Interno de Estado
            $table->enum('status', ['Pendiente', 'En Proceso', 'Completado'])->default('Pendiente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramites');
    }
};
