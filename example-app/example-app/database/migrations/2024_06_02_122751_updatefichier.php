<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fichiers', function (Blueprint $table) {
            $table->unsignedBigInteger('idfiliere')->nullable(); 
            $table->foreign('idfiliere')->references('id')->on('filières')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fichiers', function (Blueprint $table) {
            $table->dropForeign(['idfiliere']);
            $table->dropColumn('idfiliere');
        });
    }
};
