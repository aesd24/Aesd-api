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
        Schema::create('chantres_sujets_de_discussion', function (Blueprint $table) {
            $table->id();
            $table->string('Comment');
            $table->foreignId('chantre_id')->constrained('chantres')->onDelete('cascade');
            $table->foreignId('sujet_id')->constrained('sujets_de_discussion')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chantres_sujets_de_discussion');
    }
};
