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
        // Ajouter la clé étrangère à serviteurs_de_dieu 
        Schema::table('serviteurs_de_dieu', function (Blueprint $table) {
            $table->foreign('church_id')->references('id')->on('churches')->onDelete('cascade');
        });

        // Ajouter la clé étrangère à churches 
        Schema::table('churches', function (Blueprint $table) {
            $table->foreign('owner_servant_id', 'fk_owner_servant_id')
            ->references('id')->on('serviteurs_de_dieu')->onDelete('cascade');
        });

        // Ajouter la clé étrangère à fidèles 
        Schema::table('fideles', function (Blueprint $table) {
            $table->foreign('church_id', 'fk_church_id')
            ->references('id')->on('churches')->onDelete('cascade');
        });

        // Ajouter la clé étrangère à fidèles 
        Schema::table('chantres', function (Blueprint $table) {
            $table->foreign('eglise_id', 'fk_eglise_id')
            ->references('id')->on('churches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serviteurs_de_dieu', function (Blueprint $table) {
            $table->dropForeign(['church_id']);
        });
        Schema::table('churches', function (Blueprint $table) {
            $table->dropForeign(['owner_servant_id']);
        });
        Schema::table('fideles', function (Blueprint $table) {
            $table->dropForeign(['church_id']);
        });
        Schema::table('chantres', function (Blueprint $table) {
            $table->dropForeign(['eglise_id']);
        });
    }
};
