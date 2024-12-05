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
        Schema::create('church', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('adresse');
            $table->string('logo')->nullable();
            $table->boolean('is_main')->default(false);
            $table->text('description')->nullable();
            $table->string('attestation_file_path')->nullable();
            $table->string('validation_status')->nullable();

            // $table->foreignId('owner_servant_id')->nullable();
            // $table->foreignId('owner_servant_id')->nullable()->constrained('serviteurs_de_dieu')->onDelete('set null');
            $table->string('type_church');
            // $table->string('categorie');
            $table->integer('main_church_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church');
    }
};
