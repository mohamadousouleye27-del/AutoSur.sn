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
        Schema::create('inspection_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('garage_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category', 20);
            $table->date('scheduled_date');
            $table->string('time_slot', 10);
            $table->string('city', 80);
            $table->text('notes')->nullable();
            $table->unsignedInteger('fee');
            $table->string('status', 20)->default('pending'); // pending, confirmed, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_appointments');
    }
};
