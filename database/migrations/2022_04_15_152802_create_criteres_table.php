<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriteresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criteres', function (Blueprint $table) {
            $table->id();
            $table->string('typestage'); //type de stage
            $table->string('nombrequestionsfaciles'); //nombre de questions faciles
            $table->string('nombrequestionsmoyennes'); //nombre de questions moyennes
            $table->string('nombrequestionsdifficiles'); //nombre de questions difficiles
            $table->string('notequestionfacile'); //note pour chaque question facile
            $table->string('notequestionmoyenne'); //note pour chaque questio moyenne
            $table->string('notequestiondifficile'); //note pour chaque question difficile
            $table->string('etat');
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
        Schema::dropIfExists('criteres');
    }
}
