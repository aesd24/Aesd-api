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
        Schema::create('propositions_de_reponses', function (Blueprint $table) {
            $table->id();
            $table->string('intitule');
            $table->boolean('exact');
            $table->foreignId('quiz_id')->constrained('quizz')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propositions_de_reponses');
    }
};
