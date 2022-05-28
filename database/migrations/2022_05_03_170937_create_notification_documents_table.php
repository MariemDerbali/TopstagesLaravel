<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_documents', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->string('emetteur');
            // $table->string('emetteurID');
            $table->string('emetteurRole');
            $table->string('emetteurImage');
            $table->string('Stagiaire_id');
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
        Schema::dropIfExists('notification_documents');
    }
}
