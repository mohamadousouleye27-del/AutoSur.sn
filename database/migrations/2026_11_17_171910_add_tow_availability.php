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
            $table->boolean('is_available')->default(false)->after('role');
        });

        Schema::create('sos_request_declines', function (Blueprint $table) {
            $table->foreignId('sos_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['sos_request_id', 'driver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sos_request_declines');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_available'));
    }
};
