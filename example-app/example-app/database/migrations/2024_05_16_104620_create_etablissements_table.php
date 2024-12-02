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
    Schema::create('etablissements', function (Blueprint $table) {
        $table->id();
        $table->string('nom'); // Ajoutez une colonne pour le nom de l'établissement
        $table->unsignedBigInteger('institution_id')->nullable(); // Ajoutez une colonne de clé étrangère
        $table->timestamps();
        
        // Déclaration de la contrainte de clé étrangère
        $table->foreign('institution_id')->references('id')->on('institutions')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};
