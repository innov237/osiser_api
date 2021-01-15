<?php

namespace App\Http\Controllers;

use App\categorieSujet;
use Illuminate\Http\Request;

class CategorieSujetController extends Controller
{
    public function createCategorie(Request $request) {
        $request->validate([
            'libelle_categorie' => 'required|string|unique:categorie_sujets',
        ]);

        $categorie = new categorieSujet();

        $categorie->libelle_categorie = $request->libelle_categorie;
        $categorie->save();

        return response()->json(array('message'=>'categorie créée','success'=>true,200));
    }   

    public function listCategorie() {
        $liste = categorieSujet::all();

        return response()->json($liste);
    }

    public function supprimerCategorie(Request $request) {
        categorieSujet::where('id',$request->id)->delete();
        return response()->json(array('message'=>'categorie supprimé','success'=>true,200));
    }
    
}
