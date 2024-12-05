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
        Schema::create('dons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->double('objectif');
            // $table->dateTime('end_at');
            // $table->integer('status');
            // $table->string('status');
            // $table->string('recipiendaire');

            $table->dateTime('end_at')->nullable();  // Rendre end_at nullable
            $table->string('status')->nullable();  // Rendre status nullable
            $table->double('current_amount')->nullable();
            $table->string('recipiendaire')->nullable();  // Rendre recipiendaire nullable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dons');
    }
};
