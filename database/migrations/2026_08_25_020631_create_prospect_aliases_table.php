<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_aliases', function (Blueprint $table) {

            $table->id();

            $table->foreignId('prospect_id')
                ->constrained('prospects')
                ->cascadeOnDelete();

            $table->string('alias_name', 255);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique([
                'prospect_id',
                'alias_name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_aliases');
    }
};