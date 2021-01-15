<?php

namespace App\Http\Controllers;

use App\etatCommande;
use Illuminate\Http\Request;

class EtatCommandeController extends Controller
{
    public function getEtatCommande(){
      return etatCommande::all();
    }

    public function CreerEtatCommande(){

      $etat = ['En attente de traitement','En cours de traitement','Annulée','validée'];
      $count = etatCommande::count();
      if($count == 0){
        $i = 0;
        while ($i <= 3) {
          $etatCommande = new etatCommande;
          $etatCommande->libelle_etat_commande = $etat[$i];
          $etatCommande->save();
          $i++;
        }
        return response(['succes'=>true]);
      }
          return response(['succes'=>false]);
    }
}
