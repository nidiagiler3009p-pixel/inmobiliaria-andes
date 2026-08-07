<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            
            // 1. Clasificación y Gestión Interna
            $table->enum('service_type', ['Arriendo', 'Venta']);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Asesor Asignado
            $table->enum('property_type', ['Casa', 'Terrenos', 'Oficinas', 'Locales', 'Terrenos Grandes', 'Departamentos', 'Comerciales']);

            // 2. Información General y Ubicación
            $table->string('title'); // Título de la Propiedad
            $table->string('badge_left')->nullable(); // Detalle especial (Tarjetas Minimalistas Izquierda)
            $table->string('location'); // Ciudad - Cantón / Provincia
            $table->text('address'); // Dirección / Sector / Referencia
            $table->string('badge_right')->nullable(); // Referencia (Tarjetas Minimalistas Derecha, ej: a 3 cuadras...)
            $table->text('google_maps_url')->nullable();

            // 3. Distribución y Características Internas
            $table->integer('bedrooms')->nullable();
            $table->string('bedrooms_detail')->nullable(); // Ej: 1 Master con Baño
            $table->integer('bathrooms_full')->nullable(); // Con ducha
            $table->integer('bathrooms_half')->nullable(); // Sencillos / Sociales
            $table->integer('garages')->nullable();
            $table->string('garages_detail')->nullable(); // Ej: 1 Cubierto
            $table->string('social_areas')->nullable(); // Salas
            $table->string('kitchen')->nullable();
            $table->string('exteriors')->nullable(); // Patios
            $table->string('study_room')->nullable();

            // 3.1. Características Especiales y Servicios Básicos (Booleanos)
            $table->boolean('has_jardin')->default(false);
            $table->boolean('has_balcon')->default(false);
            $table->boolean('has_seguridad')->default(false);
            $table->boolean('has_agua')->default(false);
            $table->boolean('has_luz')->default(false);
            $table->boolean('has_alcantarillado')->default(false);
            $table->boolean('has_internet')->default(false);
            $table->boolean('has_piscina')->default(false);
            $table->boolean('has_bbq')->default(false);
            $table->boolean('has_amoblado')->default(false);
            $table->boolean('has_mascotas')->default(false);
            $table->boolean('price_dropped')->default(false);

            // 4. Precio, Documentación y Antigüedad
            $table->decimal('price', 12, 2);
            $table->enum('price_condition', ['Negociable', 'Fijo'])->default('Negociable');
            $table->string('documentation_status')->nullable(); // Ej: Escritura en Regla
            $table->integer('antiquity_years')->nullable();
            $table->decimal('land_area_m2', 10, 2)->nullable();
            $table->decimal('construction_area_m2', 10, 2)->nullable();

            // 5. Servicios Básicos (Opcional si mantienes compatibilidad con JSON)
            $table->json('basic_services')->nullable(); 

            // 6. Multimedia y Contenido Adicional
            $table->text('description')->nullable(); // Detalles adicionales / Observaciones
            $table->string('virtual_tour_url')->nullable(); // Tour 360°

            // 7. Contacto y Redes Sociales de Difusión
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('url_youtube')->nullable();
            $table->string('url_instagram')->nullable();
            $table->string('url_tiktok')->nullable();
            $table->string('url_facebook')->nullable();
            

            // Estado general de control interno
            $table->enum('status', ['En Venta', 'En Trámite', 'Vendida'])->default('En Venta');

            $table->timestamps();
            $table->boolean('social_info_completed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};