<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    //

	protected $table = "comment_videos";

    protected $fillable = [
    	'video_id',
    	'content', 
    	'user_id'
    ];

    protected $appends = ['owner'];


    public function videos(){
    	return $this->belongsTo(video::class, 'video_id');
    }

    public function user(){
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function getOwnerAttribute(){
    	return User::where('id', $this->user_id)->get();
    }
}
