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

            // Ajout des clés étrangères avec des noms explicites
            $table->unsignedBigInteger('serviteur_de_dieu_id');
            $table->unsignedBigInteger('sujet_id');
            
            $table->foreign('serviteur_de_dieu_id', 'fk_serviteur_id')
                ->references('id')
                ->on('serviteurs_de_dieu')
                ->onDelete('cascade');
            
            $table->foreign('sujet_id', 'fk_sujet_id')
                ->references('id')
                ->on('sujets_de_discussion')
                ->onDelete('cascade');

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
