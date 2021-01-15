<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Livraion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('livraisons', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('commande_id');
        $table->unsignedInteger('etat_id');
        $table->unsignedInteger('user_id');
        $table->date('date_livraison');
        $table->foreign('commande_id')->references('id')->on('commandes');
        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('etat_id')->references('id')->on('etat_livraisons');
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
        //
    }
}
