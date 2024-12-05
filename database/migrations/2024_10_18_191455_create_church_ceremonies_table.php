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
        Schema::create('church_ceremonies', function (Blueprint $table) {
            $table->id();
            $table->dateTime('periode_time');
            $table->foreignId('church_id')->constrained('church')->onDelete('cascade');
            $table->foreignId('ceremony_id')->constrained('ceremonies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church_ceremonies');
    }
};
