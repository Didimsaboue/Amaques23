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
        Schema::create('evaluationinterne', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idcritere');
            $table->unsignedBigInteger('idchamps');
            $table->unsignedBigInteger('idpreuve');
            $table->unsignedBigInteger('idfiliere');
            $table->unsignedBigInteger('idcampagne');
            $table->integer('score')->comment('0, 1, or -1');
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->foreign('idcritere')->references('id')->on('criteres')->onDelete('cascade');
            $table->foreign('idchamps')->references('id')->on('champs')->onDelete('cascade');
            $table->foreign('idpreuve')->references('id')->on('preuves')->onDelete('cascade');
            $table->foreign('idfiliere')->references('id')->on('filières')->onDelete('cascade');
            $table->foreign('idcampagne')->references('id')->on('invitations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluationinterne');
    }
};
