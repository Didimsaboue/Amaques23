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
            $table->date('date_habilitation')->nullable();
            $table->date('date_accreditation')->nullable();
            $table->date('date_fin_accreditation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filières', function (Blueprint $table) {
            $table->dropColumn(['date_habilitation', 'date_accreditation', 'date_fin_accreditation']);
        });
    }
};
