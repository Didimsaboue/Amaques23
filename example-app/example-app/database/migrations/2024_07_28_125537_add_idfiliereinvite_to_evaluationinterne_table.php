<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdfiliereinviteToEvaluationinterneTable extends Migration
{
    public function up()
    {
        Schema::table('evaluationinterne', function (Blueprint $table) {
            $table->unsignedBigInteger('idfiliereinvite')->nullable()->after('idfiliere');

            // If you have foreign key constraints
            $table->foreign('idfiliereinvite')->references('id')->on('filieresinvites')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('evaluationinterne', function (Blueprint $table) {
            $table->dropForeign(['idfiliereinvite']);
            $table->dropColumn('idfiliereinvite');
        });
    }
}
