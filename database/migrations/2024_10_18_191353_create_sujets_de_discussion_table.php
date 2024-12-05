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
        Schema::create('sujets_de_discussion', function (Blueprint $table) {
            $table->id();
            $table->string('theme');
            $table->date('date');
            $table->text('body');
            $table->foreignId('administrateur_id')->constrained('administrateurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sujets_de_discussion');
    }
};
