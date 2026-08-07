<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_accounting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');

            $table->enum('transaction_type', ['Venta Directa', 'Alquiler', 'Traspaso', 'Permuta']);
            $table->decimal('gross_commission', 12, 2); // Comisión Bruta ingresada
            
            $table->decimal('variable_expenses', 12, 2)->default(0);
            $table->decimal('fixed_expenses_prorated', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('net_company_profit', 12, 2); // Ganancia neta real

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_accounting');
    }
};