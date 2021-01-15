<?php

namespace App\Http\Controllers;

use App\discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function creeDiscussion(Request $request) {
        $discussion = new discussion();

        $discussion->abonner_id = $request->abonner_id;
        $discussion->reply_id = $request->reply_id;
        $discussion->message = $request->message;

        $discussion->save();

        return response()->json(array('success'=>true,200));
    }

    public function listDiscussionSujet(Request $request) {
        $discussion = [];
        $list = discussion::join('abonners','abonners.id','=','discussions.abonner_id')
                          ->join('type_abonners','type_abonners.id','=','abonners.type_id')
                          ->join('sujets','sujets.id','=','abonners.sujet_id')
                          ->join('users','users.id','=','abonners.user_id')
                          ->where('sujets.id',$request->key)
                          ->select(
                              'discussions.*',
                              'users.nom',
                              'users.id as user_id',
                              'type_abonners.type'
                            )
                          ->get();
        foreach ($list as $record) {
            $list_reply = discussion::join('abonners','abonners.id','=','discussions.abonner_id')
                          ->join('type_abonners','type_abonners.id','=','abonners.type_id')
                          ->join('sujets','sujets.id','=','abonners.sujet_id')
                          ->join('users','users.id','=','abonners.user_id')
                          ->where('discussions.id',$record->reply_id)
                          ->select(
                              'discussions.*',
                              'users.nom',
                              'users.id as user_id',
                              'type_abonners.type'
                            )
                          ->first();
            $record->reply = $list_reply;
            $discussion[] = $record;
        }
        return response()->json($discussion);   
    }

    public function supprimerDiscussion(Request $request) {
        $discussion = discussion::find($request->id);
        $discussion->delete();
        return response()->json(array('success'=>true,200));
    }
}
