<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('devise_id');
            $table->unsignedInteger('pays_id');
            $table->String('code');
            $table->String('nom');
            $table->String('identifiant');
            $table->String('drapeau',50)->unique()->nullable();
            $table->foreign('devise_id')->references('id')->on('devices');
            $table->foreign('pays_id')->references('id')->on('pays');
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
        Schema::dropIfExists('pays');
    }
}
