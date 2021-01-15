<?php

namespace App\Http\Controllers;

use App\pays;
use App\devise;
use Illuminate\Http\Request;

class PaysController extends Controller
{
    // public function listPaysDevise()
    // {
    //     $pays = devise::find(1)->Pays;
    //     return response()->json($pays);
    // }

     public function listePays(){
        $pays = pays::join('devises','pays.devise_id','=','devises.id')
        ->select(
            'pays.*',
            'pays.code as codePays',
            'devises.code as codeDevise',
            'devises.*'
            )
            ->get();
        return response()->json($pays);
    }

    public function ajouterPays(request $request){
        $request->validator([
            'devise_id'=>'required|integer',
            'code'=>'required|string|unique:pays',
            'nom'=>'required|string|unique:pays',
            'identifiant'=>'required|string|unique:pays',
            'drapeau'=>'',
        ]);
        
        $pays = new pays();
        $pays->devise_id = $request->devise_id;
        $pays->code = $request->code;
        $pays->nom = $request->nom;
        $pays->identifiant = $request->identifiant;
        $pays->drapeau = $request->drapeau;

        $pays->save();

        return response()->json(array('message'=>'pays enregistré avec succes', 'success'=>true,201));
    }

     public function telechargerImage(Request $request)
    {
        if ($request->hasFile('image')) {

            $files = $request->file('image');
            $request->image->storeAs('public', $files->getClientOriginalName());
            return response()->json(array('message' => 'image upload success'));
        }
        return response()->json(array('message' => 'image upload error'));
    }
}
