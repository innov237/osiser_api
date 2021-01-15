<?php

namespace App\Http\Controllers;

use App\typeUser;
use Illuminate\Http\Request;

class TypeUserController extends Controller
{
    public function ajouterType(Request $resquest){
        $resquest->validate([
            'libelleType' => 'required|string|unique:type_users',
        ]);

        $type = new typeUser;
        $type->libelleType = $resquest->libelleType;
        $type->save();
        
        return response()->json(array('message' =>'type crée avec success' ,'success'=>true ));
    }

    public function listeType(){
        $type = typeUser::all();

        return response()->json($type);
    }
}
