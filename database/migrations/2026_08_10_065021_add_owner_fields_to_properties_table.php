<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('owner_name')->nullable()->after('user_id');
            $table->string('owner_phone')->nullable()->after('owner_name');
            $table->string('owner_dni')->nullable()->after('owner_phone');
            $table->string('owner_email')->nullable()->after('owner_dni');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'owner_phone', 'owner_dni', 'owner_email']);
        });
    }
};