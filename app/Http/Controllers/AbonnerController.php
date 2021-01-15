<?php

namespace App\Http\Controllers;

use App\abonner;
use App\typeAbonner;
use Illuminate\Http\Request;

class AbonnerController extends Controller
{
    public function abonnement(Request $request) {

        $type = typeAbonner::find($request->type_id);

        if ($type->type == "Expert"){
            $abonner = new abonner();
            $abonner->sujet_id = $request->sujet_id;
            $abonner->type_id = $request->type_id;
            $abonner->user_id = $request->user_id;
            $abonner->etat = 'en attente';
            $abonner->save();
            return response()->json(array('message'=>'abonnement en cour','success'=>true,200));
        }else {
            $abonner = new abonner();
            $abonner->sujet_id = $request->sujet_id;
            $abonner->type_id = $request->type_id;
            $abonner->user_id = $request->user_id;
            $abonner->etat = 'valider';
            $abonner->save();
            return response()->json(array('message'=>'abonnement reussit','success'=>true,200));
        }
    }
    
    public function rejetAbonnement(Request $request){
        abonner::where('id',$request->id)->update(['type_id'=>$request->type_id, 'etat'=>'valider']);
        return response()->json(array('message'=>'abonnement rejeté pour Expert','success'=>true,200));
    }

    public function demandeAbonnement() {
        
        $list = abonner::join('users','users.id','=','abonners.user_id')
                        ->join('type_abonners','type_abonners.id','=','abonners.type_id')
                        ->join('sujets','sujets.id','=','abonners.sujet_id')
                        ->join('categorie_sujets','categorie_sujets.id','=','sujets.categorie_id')
                        ->where('abonners.etat','en attente')
                        ->select('abonners.*','type_abonners.type','users.nom','users.telephone','sujets.titre as libelle_sujet','categorie_sujets.libelle_categorie')
                        ->get();
        return response()->json($list);
    }
    
    public function valideAbonnementExpert(Request $request){
        abonner::where('id',$request->id)->update(['etat'=> 'valider']);
        return response()->json(array('message'=>'abonnement reussit','success'=>true,200));
    }
    
    public function deleteAbonner(Request $request) {
        abonner::where('id',$request->id)->delete();
        return response()->json(array('message'=>'abonnement supprimer','success'=>true,200));
    }

    public function deactivateAbonner(Request $request) {
        abonner::where('id',$request->id)
                ->update(
                    ['etat' => 'bloquer']
                );
        return response()->json(array('message'=>'abonnement bloquer','success'=>true,200));
    }
}
