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
        Schema::create('filieresinvites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idfiliere');
            $table->unsignedBigInteger('idcampagne');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->timestamps();
            $table->foreign('idfiliere')->references('id')->on('filières')->onDelete('cascade');
            $table->foreign('idcampagne')->references('id')->on('invitations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filieresinvites');
    }
};
