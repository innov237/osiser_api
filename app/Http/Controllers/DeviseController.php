<?php

namespace App\Http\Controllers;

use App\devise;
use App\pays;
use Illuminate\Http\Request;

class DeviseController extends Controller
{
    public function ajouterDevise(Request $request)
    {
      
        $request->validate([
            'libelle' => 'required|string|unique:devises',
            'code' => 'required|string|unique:devises',
            'taux' => '',
            'symbole' => 'required',
            'position' => 'required',
        ]);
        $devise = new devise;
        $devise->libelle = $request->libelle;
        $devise->code = $request->code;
        $nbredevise = $this::devise_vide();
        
        if($nbredevise == 0){
            $devise->taux = 1;     
        }else{
            $devise->taux = $request->taux;
        }
       
        $devise->symbole = $request->symbole;
        $devise->position = $request->position;
        $devise->save();
        return response()->json(array('message' => 'devise enregistré', 'success' => true, 201));
    }

    public function listDevise()
    {
        $devise = devise::all();
        return response()->json($devise);
    }

    public function DeviseDefault()
    {
        $devise = devise::where('taux',1)->first();
        return response()->json($devise);
    }

    public function ajouterPays(Request $request)
    {
        $request->validate([
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

    public function listPays()
    {
        $pays = pays::all();
        return response()->json($pays);
    }


    public function devise_vide(){
        $count = devise::count();
        return $count;
    }
}
