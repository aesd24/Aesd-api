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
        Schema::create('users_dons', function (Blueprint $table) {
            $table->id();
            $table->string('reference_paiement');
            $table->date('date_paiement');
            $table->decimal('montant_paiement', 10, 3);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('don_id')->constrained('dons')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_dons');
    }
};
