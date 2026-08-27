<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_contacts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('prospect_id')
                ->constrained('prospects')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | TIPO DE CONTACTO
            |--------------------------------------------------------------------------
            |
            | Valores esperados:
            | phone
            | instagram
            | facebook
            | tiktok
            | whatsapp
            |
            */

            $table->string('type', 50);

            /*
            |--------------------------------------------------------------------------
            | VALOR
            |--------------------------------------------------------------------------
            |
            | Ejemplos:
            | 0987654321
            | https://instagram.com/usuario
            | https://facebook.com/usuario
            |
            */

            $table->string('value', 500);

            /*
            |--------------------------------------------------------------------------
            | ETIQUETA
            |--------------------------------------------------------------------------
            |
            | Ejemplos:
            | Alternativo
            | Personal
            | Trabajo
            |
            */

            $table->string('label', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | CONTACTO PRINCIPAL
            |--------------------------------------------------------------------------
            |
            | El teléfono principal sigue estando en prospects.phone.
            | Aquí normalmente guardaremos el alternativo.
            |
            */

            $table->boolean('is_primary')
                ->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | EVITAR DUPLICADOS EXACTOS
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'prospect_id',
                'type',
                'value'
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('prospect_contacts');
    }
};