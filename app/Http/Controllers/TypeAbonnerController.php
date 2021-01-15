<?php

namespace App\Http\Controllers;

use App\typeAbonner;
use Illuminate\Http\Request;

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
}
