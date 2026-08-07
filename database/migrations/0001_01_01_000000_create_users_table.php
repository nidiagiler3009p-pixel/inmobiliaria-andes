<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // 1. Datos Personales y de Contacto
            $table->string('name'); // Nombre completo
            $table->string('last_name'); // Apellido completo
            $table->string('email')->unique(); // Correo electrónico
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Credenciales de acceso a la intranet
            $table->string('phone'); // Celular (Ej: +593 ...)
            $table->string('city'); // Ciudad de residencia (Ej: Cuenca, Ecuador)

            // 2. Perfil Profesional y Asignación de Rol
            $table->string('profession'); // Profesión / Área de interés
            $table->string('experience_years'); // Experiencia laboral (años)
            
            // Rol ampliado según los requerimientos
            $table->enum('role', [
                'Trámites', 
                'Asesor', 
                'Contador', 
                'Publicista', 
                'Administrador/Gerente'
            ])->default('Asesor');

            $table->integer('monthly_goal')->default(2); // Meta (ej. propiedades vendidas al mes)
            $table->string('branch')->nullable(); // Sucursal corporativa opcional

            // 3. Aporte Inmobiliario y Documentación
            $table->boolean('contributes_properties')->default(false); // Aporto con propiedades al catálogo (Sí/No)
            $table->string('cv_file_path')->nullable(); // Ruta del archivo PDF de la hoja de vida (Máx 5MB)

            // 4. Control de Tiempos y Estado
            $table->date('hire_date')->useCurrent(); // Fecha de ingreso para calcular el tiempo en la empresa
            $table->enum('status', ['Activo', 'Inactivo', 'Suspendido'])->default('Activo');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};