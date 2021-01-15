<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commentaires', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('commentaire_id')->nullable();
            $table->unsignedInteger('produit_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->String('comment');
            $table->date('date_comment');
            $table->foreign('commentaire_id')->references('id')->on('commentaires');
            $table->foreign('produit_id')->references('id')->on('produits');
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
        Schema::dropIfExists('commentaires');
    }
}
