<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_portfolio_entries', function (Blueprint $table) {

            $table->foreignId('prospect_id') ->nullable()->after('client_id')->constrained('prospects')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_portfolio_entries', function (Blueprint $table) {
              $table->dropForeign(['prospect_id']);
    $table->dropColumn('prospect_id');
        });
    }
};
