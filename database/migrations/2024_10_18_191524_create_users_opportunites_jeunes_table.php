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
        Schema::create('users_opportunites_jeunes', function (Blueprint $table) {
            $table->id();
            $table->string('CV');
            $table->string('Lettre de Motivation');
            $table->string('Détails');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('opportunite_id')->constrained('opportunites_jeunes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_opportunites_jeunes');
    }
};
