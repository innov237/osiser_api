<?php

namespace App\Http\Controllers;

use App\Comment;
use App\video;
use Illuminate\Http\Request;

class CommentVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json(video::with(['comments'])->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'video_id' => 'required|integer',
            'user_id' => 'required|integer',
            'content' => 'required'
        ]);

        $comment = Comment::create($request->only(['video_id', 'user_id', 'content']));

        return response()->json([
            'success' => true,
            'messages' => 'Enregistrement effectué'
        ]);  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        //return response()->json(Comment::with(['videos' , 'user'])->where('id', $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
