<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvitationToFiliereinvitesTable extends Migration
{
    public function up()
    {
        Schema::table('filieresinvites', function (Blueprint $table) {
            $table->boolean('invitation')->default(0); // Ajoute le champ invitation avec une valeur par défaut de 0
        });
    }

    public function down()
    {
        Schema::table('filieresinvites', function (Blueprint $table) {
            $table->dropColumn('invitation');
        });
    }
}
