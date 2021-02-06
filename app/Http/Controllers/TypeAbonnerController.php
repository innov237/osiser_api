<?php

namespace App\Http\Controllers;

use App\typeAbonner;
use App\abonner;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

class TypeAbonnerController extends Controller
{
    public function createType(Request $request) {
        $request->validate([
            'type' => 'required|string',
        ]);

        $type = new typeAbonner();

        $type->type = $request->type;
        $type->save();

        return response()->json(array('message'=>'type crée','success'=>true,200));
    }   

    public function listType() {
        $liste = typeAbonner::all();

        return response()->json($liste);
    }

    public function update(Request $request) {
        
        $credentials = Validator::make($request->all(),[
            'type_id' => 'required|exists:type_abonners,id',
            'sujet_id' => 'required|exists:type_abonners,id',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($credentials->fails()) {
            return response()->json(
                array(
                    'success'=>false,
                    'message' => 'UNPROCESS',
                    'detail'=>$credentials->errors()
                ), 422
            );
        }

        $render = abonner::where([
                    ['user_id', $request->input('user_id')],
                    ['sujet_id', $request->input('sujet_id')]
                ])
                ->update([
                    'type_id' => $request->input('type_id')
                ]);

        if ($render)
            return response()->json(
                array(
                    'success'=>true,
                ), 200
            );
        else
            return response()->json(
                array(
                    'success'=>false,
                    'message' => 'NOT FOUND',
                    'detail'=> "User abonnenent not found"
                ), 404
            );
    }
}
