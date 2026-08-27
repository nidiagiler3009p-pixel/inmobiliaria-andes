<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_portfolio_entries', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELACIONES
            |--------------------------------------------------------------------------
            */

            // Cliente registrado en clients
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Cita desde donde llegó a cartera
            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained('appointments_tracking')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Propiedad de interés
            $table->foreignId('property_id')
                ->nullable()
                ->constrained('properties')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Asesor responsable
            $table->foreignId('advisor_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | ORIGEN DEL REGISTRO
            |--------------------------------------------------------------------------
            |
            | Ejemplos:
            | Cita
            | Cita Cancelada
            | Cita Realizada
            | Ingreso Manual
            | Formulario Web
            |
            */

            $table->string('entry_source', 100)
                ->default('Cita');


            /*
            |--------------------------------------------------------------------------
            | CANAL DE CONTACTO
            |--------------------------------------------------------------------------
            |
            | Ejemplos:
            | Sitio Web
            | WhatsApp
            | Facebook
            | Instagram
            | TikTok
            | Teléfono
            | Correo
            | Referido
            | Presencial
            |
            */

            $table->string('contact_channel', 100)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | RED SOCIAL
            |--------------------------------------------------------------------------
            */

            // Ejemplo: Facebook, Instagram, TikTok
            $table->string('social_platform', 50)
                ->nullable();

            // Link de la cuenta/perfil del cliente
            $table->string('social_profile_url', 500)
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | INFORMACIÓN COMERCIAL
            |--------------------------------------------------------------------------
            */

            // Por qué fue enviado a cartera
            $table->text('entry_reason')
                ->nullable();

            // Estado propio dentro de cartera
            $table->string('portfolio_status', 50)
                ->default('Nuevo');

            // Observaciones del asesor
            $table->text('notes')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | FECHAS
            |--------------------------------------------------------------------------
            */

            // Fecha en que ingresó a cartera
            $table->timestamp('entered_at')
                ->nullable();

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index('portfolio_status');
            $table->index('contact_channel');
            $table->index('advisor_id');

            // Evita pasar accidentalmente la misma cita
            // dos veces a cartera.
            $table->unique('appointment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_portfolio_entries');
    }
};