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
        Schema::table('filières', function (Blueprint $table) {
            $table->boolean('lessence')->default(1);
            $table->boolean('master')->default(0);
            $table->boolean('doctorat')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filières', function (Blueprint $table) {
         
        });
    }
};
