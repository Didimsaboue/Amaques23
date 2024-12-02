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
        Schema::create('départements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
         $table->unsignedBigInteger('etablissements_id')->nullable(); 

  
        // Déclaration de la contrainte de clé étrangère
        $table->foreign('etablissements_id')->references('id')->on('etablissements')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('départements');
    }
};
