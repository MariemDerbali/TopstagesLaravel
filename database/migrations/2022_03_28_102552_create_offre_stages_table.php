<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffreStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offre_stages', function (Blueprint $table) {
            $table->id();
            $table->string('sujet');
            $table->string('domaine');
            $table->string('type');
            $table->string('description');
            $table->string('technologies');
            $table->string('periode');
            $table->string('encadrant');
            $table->string('etatoffre');
            $table->string('etatpartage');
            $table->string('annee');
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
        Schema::dropIfExists('offre_stages');
    }
}
