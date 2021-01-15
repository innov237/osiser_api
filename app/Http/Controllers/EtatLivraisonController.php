<?php

namespace App\Http\Controllers;

use App\etatLivraison;
use Illuminate\Http\Request;

class EtatLivraisonController extends Controller
{
   public function getEtatlivraison(){
       return etatLivraison::all();
   }

   public function CreerEtatLivraison(){

     $etat = ['En cour','effectuée','retour','En attente'];
     $count = etatLivraison::count();
     if($count == 0){
       for ($i=0 ; $i < 4  ; $i++ ) {
         $etatLivraison = new etatLivraison;
         $etatLivraison->libelle_etat_livraison = $etat[$i];
         $etatLivraison->save();
       }
          return response(['succes'=>true]);
     }
      return response(['succes'=>false]);
   }
}
