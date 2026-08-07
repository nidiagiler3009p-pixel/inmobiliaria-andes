<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            // Si user_id es NULL, es el link global de la empresa (footer). Si tiene ID, es del asesor.
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            // Usamos string para que acepte redes sociales, bancos o cooperativas libremente
            $table->string('platform'); 
            
            $table->string('url_or_value'); // Aquí irá el número de cuenta, link de pago o usuario
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};