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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('payable');                         // ce qui est payé (rendez-vous, plus tard assurance…)
            $table->unsignedInteger('amount');                 // en FCFA
            $table->string('currency', 3)->default('XOF');
            $table->string('provider', 20);                    // simulation, wave…
            $table->string('status', 20)->default('pending');  // pending, paid, failed, expired
            $table->uuid('reference')->unique();
            $table->string('provider_session_id')->nullable()->index();
            $table->text('checkout_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
