<?php

namespace App\Http\Controllers;

use App\video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function ajouterVideos(Request $request) {
        $request->validate([
            'titre' => 'required|unique:videos',
            'link' => 'required|unique:videos'
        ]);

        $video = new video();
        $video->titre = $request->titre;
        $video->description = $request->description;
        $video->link = $request->link;

        $video->save();

        return response()->json(array('message'=>'video enregistré','success'=>200));
    }

    public function listVideo() {
        $video = video::all();
        return response()->json($video);
    }

    public function supprimerVideo(Request $request){
        video::where('id',$request->id)->delete();
        return response()->json(array('message'=>'video enregistré','success'=>200));
    }
}
