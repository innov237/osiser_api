<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('video_id');

            //$table->foreign('user_id')->references('id')->on('users');
            //$table->foreign('video_id')->references('id')->on('videos');
            
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
        Schema::dropIfExists('comment_videos');
    }
}
