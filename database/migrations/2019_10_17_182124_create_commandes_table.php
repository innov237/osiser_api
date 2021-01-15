<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedInteger('user_id');
          $table->unsignedInteger('etat_id');
          $table->unsignedInteger('addresse_id');
          $table->date('date_commande');
          $table->foreign('user_id')->references('id')->on('users');
          $table->foreign('etat_id')->references('id')->on('etat_commandes');
          $table->foreign('addresse_id')->references('id')->on('addresses');
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
        Schema::dropIfExists('commandes');
    }
}
