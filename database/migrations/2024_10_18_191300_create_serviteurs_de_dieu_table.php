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
        Schema::create('serviteurs_de_dieu', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_main')->default(false);
            $table->boolean('is_assigned')->default(false);
            $table->string('id_card_recto')->nullable()->unique();
            $table->string('id_card_verso')->nullable()->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('church_id')->nullable(); // Ajout ici sans clé étrangère 
            $table->timestamps();        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serviteurs_de_dieu');
    }
};
