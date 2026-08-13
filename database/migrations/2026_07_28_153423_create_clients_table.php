<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Asesor a cargo
            
            $table->string('name');
            $table->string('last_name');
            $table->string('identification_card')->nullable();
            $table->string('phone');
            $table->string('email')->nullable()->change();
            
            $table->string('social_media_source')->nullable(); // WhatsApp, Instagram DM, Facebook Messenger, TikTok
            $table->enum('status', [
                'Confirmada', 'Interesado', 'En Proceso', 
                'Cerrado Exitoso', 'Seguimiento Pendiente', 'Negociación', 'Vendida'
            ])->default('Interesado');
            
            $table->string('origin_module'); // Contacto, Trámite, Asesoría
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
