<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('properties', function (Blueprint $table) {
        // Agrega el campo para el nombre del dueño (opcional si deseas agregar también el teléfono)
        $table->string('owner_name')->nullable()->after('title');
        $table->string('owner_phone')->nullable()->after('owner_name'); // Opcional
    });
}

public function down(): void
{
    Schema::table('properties', function (Blueprint $table) {
        $table->dropColumn(['owner_name', 'owner_phone']);
    });
}
};
