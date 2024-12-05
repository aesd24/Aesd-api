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
        Schema::create('serviteurs_de_dieu_sujets_de_discussion', function (Blueprint $table) {
            $table->id();
            $table->string('Comment');
            $table->foreignId('serviteur_de_dieu_id')->constrained('serviteurs_de_dieu')->onDelete('cascade');
            $table->foreignId('sujet_id')->constrained('sujets_de_discussion')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serviteurs_de_dieu_sujets_de_discussion');
    }
};
