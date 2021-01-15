<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbonnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abonners', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sujet_id');
            $table->unsignedInteger('type_id');
            $table->unsignedInteger('user_id');
            $table->string('etat');
            $table->foreign('sujet_id')->references('id')->on('sujets')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('type_abonners')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('abonners');
    }
}
