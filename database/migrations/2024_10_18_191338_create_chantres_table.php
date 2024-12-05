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
        Schema::create('chantres', function (Blueprint $table) {
            $table->id();
            // $table->string('manager');
            $table->string('manager')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('church_id')->constrained('church')->onDelete('cascade');
            $table->foreignId('church_id')->nullable()->constrained('church')->onDelete('cascade'); // Ajout de nullable ici
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chantres');
    }
};