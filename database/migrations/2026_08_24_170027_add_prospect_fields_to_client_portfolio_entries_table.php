
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('client_portfolio_entries', function (Blueprint $table) {
            $table->string('prospect_name', 255)->nullable()->after('previous_status');
            $table->string('prospect_last_name', 255)->nullable()->after('prospect_name');
            $table->string('prospect_phone', 50)->nullable()->after('prospect_last_name');
            $table->string('prospect_email', 255)->nullable()->after('prospect_phone');
        });
    }

    public function down(): void {
        Schema::table('client_portfolio_entries', function (Blueprint $table) {
            $table->dropColumn(['prospect_name', 'prospect_last_name', 'prospect_phone', 'prospect_email']);
        });
    }
};
