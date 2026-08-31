<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();

            // Nombre de la categoría:
            // Arriendo, Internet, Combustible, Publicidad, etc.
            $table->string('name');

            // Código interno opcional para facilitar reportes.
            $table->string('code')->nullable()->unique();

            // Clasificación contable general.
            // Se deja como string para que pueda evolucionar sin depender de ENUM.
            $table->string('expense_type')->default('General');

            // Descripción opcional.
            $table->text('description')->nullable();

            // Permite desactivar una categoría sin eliminar su historial.
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};