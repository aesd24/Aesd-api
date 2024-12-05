<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::table('church', function (Blueprint $table) {

//             if (Schema::hasColumn('church', 'owner_servant_id')) {
//                 $table->foreign('owner_servant_id')->references('id')->on('serviteurs_de_dieu')->onDelete('set null');
//             }
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::table('church', function (Blueprint $table) {
//             $table->dropForeign(['owner_servant_id']);
//         });
//     }

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
        Schema::table('church', function (Blueprint $table) {
            // Vérifie et ajoute la colonne si elle n'existe pas
            if (!Schema::hasColumn('church', 'owner_servant_id')) {
                $table->foreignId('owner_servant_id')->nullable()->constrained('serviteurs_de_dieu')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('church', function (Blueprint $table) {
            if (Schema::hasColumn('church', 'owner_servant_id')) {
                $table->dropForeign(['owner_servant_id']);
                $table->dropColumn('owner_servant_id');
            }
        });
    }
};


