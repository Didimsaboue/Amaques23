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
        Schema::create('filières', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // Ajoutez une colonne pour le nom de l'établissement
            $table->unsignedBigInteger('départements_id')->nullable(); 
            $table->timestamps();
            
            // Déclaration de la contrainte de clé étrangère
            $table->foreign('départements_id')->references('id')->on('départements')->onDelete('set null');

            });
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filières');
    }
};
