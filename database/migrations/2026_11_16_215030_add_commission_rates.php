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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('commission_rate', 4, 2)->default(8.00)->after('role');
        });

        Schema::table('insurance_offers', function (Blueprint $table) {
            $table->decimal('commission_rate', 4, 2)->default(8.00)->after('annual_premium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('commission_rate'));
        Schema::table('insurance_offers', fn (Blueprint $table) => $table->dropColumn('commission_rate'));
    }
};
