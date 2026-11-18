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
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('partner_status', 20)->nullable()->after('is_active'); // pending, approved, rejected
        });

        // Les partenaires déjà créés (tests) restent opérationnels.
        DB::table('users')
            ->whereIn('role', ['garage', 'depanneur', 'assurance'])
            ->update(['partner_status' => 'approved']);

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['is_active', 'partner_status']));
    }
};
