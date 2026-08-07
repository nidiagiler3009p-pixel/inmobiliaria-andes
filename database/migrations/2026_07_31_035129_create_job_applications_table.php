<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('celular');
            $table->string('correo');
            $table->enum('profesion', [
    'asesor comercial', 
    'publicista y marketing', 
    'contabilidad', 
    'administrativo', 
    'area legal'
]);
            $table->string('ciudad');
            $table->string('experiencia');
            $table->string('cv_path'); // Ruta del archivo PDF guardado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};