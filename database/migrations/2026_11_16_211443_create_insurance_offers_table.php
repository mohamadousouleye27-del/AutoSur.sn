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
        Schema::create('insurance_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insurance_quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('insurer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('annual_premium');        // en FCFA
            $table->date('valid_until')->nullable();
            $table->text('details')->nullable();
            $table->string('status', 20)->default('pending'); // pending, accepted, rejected
            $table->timestamps();

            $table->unique(['insurance_quote_id', 'insurer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_offers');
    }
};
