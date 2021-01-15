<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('categorie_id');
            $table->unsignedInteger('devise_id');
            $table->unsignedInteger('user_id');
            $table->String('libelle');
            $table->Integer('prix_achat');
            $table->Integer('prix_vente');
            $table->String('keyword', 500);
            $table->Integer('note');
            $table->Integer('reduction');
            $table->String('description', 500);
            $table->String('slug', 500);
            $table->Integer('quantite');
            $table->json('image');
            $table->boolean('est_actif')->default(1);
            $table->boolean('is_promo')->default(0);
            $table->foreign('categorie_id')->references('id')->on('categories');
            $table->foreign('devise_id')->references('id')->on('devises');
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
        Schema::dropIfExists('produits');
    }
}
