<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriqueStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historique_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('produit_id');
            $table->unsignedInteger('devise_id');
            $table->unsignedInteger('categorie_id');
            $table->unsignedInteger('user_id');
            $table->String('libelle');
            $table->String('prix_achat');
            $table->String('prix_vente');
            $table->String('keyword',500);
            $table->String('description',500);
            $table->Integer('quantite');
            $table->foreign('produit_id')->references('id')->on('produits');
            $table->foreign('devise_id')->references('id')->on('devices');
            $table->foreign('categorie_id')->references('id')->on('categories');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('historique_stocks');
    }
}
