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
        Schema::create('opportunites_jeunes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->string('post_profile');
            $table->string('exigence');
            $table->time('deadline');
            $table->string('localisation_du_poste');
            // $table->time('is_published_at')->nullable();
            $table->dateTime('is_published_at')->nullable();
            $table->string('study_level');
            $table->string('experience');
            $table->string('type_contract');
            $table->foreignId('administrateur_id')->constrained('administrateurs')->onDelete('cascade');
            $table->string('company_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunites_jeunes');
    }
};
