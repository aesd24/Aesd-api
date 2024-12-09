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
        Schema::create('churches', function (Blueprint $table) {
            $table->id(); // Crée la clé primaire d'abord
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('adresse');
            $table->string('logo')->nullable();
            $table->boolean('is_main')->default(false);
            $table->text('description')->nullable();
            $table->string('attestation_file_path')->nullable();
            $table->enum('validation_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('type_church');
            $table->integer('main_church_id')->nullable();
            $table->unsignedBigInteger('owner_servant_id')->nullable(); // Ajout ici sans clé étrangère
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('churches');
    }
};
