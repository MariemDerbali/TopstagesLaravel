<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_stages', function (Blueprint $table) {
            $table->id();
            $table->string('domaine');
            $table->string('type');
            $table->string('ficherep');
            $table->string('cv');
            $table->string('stagiaireID');
            $table->string('nom');
            $table->string('prenom');
            $table->string('cinpasseport');
            $table->string('email');
            $table->string('etatpost');
            $table->string('etatdemande');
            $table->string('etataccepter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demande_stages');
    }
}
