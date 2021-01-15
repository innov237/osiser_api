<?php

namespace App\Http\Controllers;

use App\sujet;
use App\abonner;
use App\categorieSujet;
use Illuminate\Http\Request;

class SujetController extends Controller
{
    public function creerSujet(Request $request) {
        $request->validate([
            'titre' => 'required|string',
        ]);

        $sujet = new sujet();

        $sujet->titre = $request->titre;
        $sujet->description = $request->description;
        $sujet->categorie_id = $request->categorie_id;

        $sujet->save();

        return response()->json(array('message'=>'Sujet crée','success'=>true,200));

    }  

    public function modifierSujet(Request $request) {
        $request->validate([
            'titre' => 'required|string',
        ]);

        $sujet = sujet::find($request->id);

        $sujet->titre = $request->titre;
        $sujet->description = $request->description;
        $sujet->categorie_id = $request->categorie_id;

        $sujet->save();

        return response()->json(array('message'=>'Sujet modifié','success'=>true,200));

    }  
    
    public function listSujetCategorie() {
        $listeCategorie = categorieSujet::all();

        $liste = [];

        foreach ($listeCategorie as $record) {
            
            $listeSujet = sujet::join('categorie_sujets','categorie_sujets.id','=','sujets.categorie_id')
                                ->where('categorie_id',$record->id)
                                ->select('sujets.*','categorie_sujets.libelle_categorie')
                                ->get();

            $listeAbonner = [];
            foreach ($listeSujet as $key) {
                $Abonner = abonner::join('sujets','sujets.id','=','abonners.sujet_id')
                                       ->join('users','users.id','=','abonners.user_id')
                                       ->join('type_abonners','type_abonners.id','=','abonners.type_id')
                                       ->where('abonners.etat','valider')
                                       ->select('abonners.*','type_abonners.type')
                                       ->get();
                $key->abonner = $Abonner;
                $listeAbonner[] = $key;
            }                          

            $record->sujets = $listeSujet;

            $liste[] = $record;
        }

        return response()->json($liste);
    }

    public function deleteSujet(Request $request) {
        sujet::where('id',$request->id)->delete();
        return response()->json(array('message'=>'Sujet supprimé','success'=>true,200));
    }

    public function listDiscussionSujet(Request $request, $limit) {
        $liste = abonner::join('sujets','sujets.id','=','abonners.sujet_id')
        ->join('users','users.id','=','abonners.user_id')
        ->join('type_abonners','type_abonners.id','=','abonners.type_id')
        ->join('discussions','discussions.abonner_id','=','abonners.id')
        ->where('sujets.id',$request->id)
        ->select(
            'discussions.*',
            'type_abonners.type',
            'users.name'
        )
        ->get();

        return response()->json($liste);
    }
}
