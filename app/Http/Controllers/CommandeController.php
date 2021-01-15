<?php

namespace App\Http\Controllers;

use App\commande;
use App\produit;
use App\etatCommande;
use App\ligneCommande;
use App\livraison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function commandeClient(Request $request){

        $commande = new commande;
        $commande->user_id = $request->input('client_id');
        $commande->addresse_id = $request->input('addresse_id');
        $commande->date_commande = date('Y/m/d');
        $commande->montant_commande = $request->input('montant_commande');
        $commande->etat_id = 1;
        $commande->save();
        $id_commande = $commande->getKey();

        $result = (array) $request->input('data');
        foreach ($result as $data) {
            $ligneCommande = new ligneCommande;
            $ligneCommande->produit_id = $data['id'];
            $ligneCommande->commande_id = $id_commande;
            $ligneCommande->quantite = $data['quantite'];
            $ligneCommande->prix = $data['prix'];
            $ligneCommande->save();

            $quantite = produit::where('id', $data['id'])
                ->select('quantite')
                ->first();

            $produit = produit::find($data['id']);
            $produit->quantite = $quantite->quantite - $data['quantite'];
            $produit->save();
        }

        // $commande = commande::find($id_commande);
        // $commande->etat_commande = "En cour de traitement";
        // $commande->save();
        //

        if($commande->save() && $produit->save()){
            return response()->json(array('message' => 'commande effectuée', 'success'=>true, 201));
        }
        return response()->json(array('message' => 'commande non effectuée', 'success'=>false, 401));
    }

    public function verifieCommande(Request $request)
    {
        $error = [];
        $result = (array) $request->input('data');
        foreach ($result as $data) {
            $quantiteStock = produit::where('id', $data['id'])->first();
            if ($data['quantite'] > $quantiteStock->quantite) {
                array_push($error, $quantiteStock->libelle . " quantite insuffisante: " . $quantiteStock->quantite . " en stock");
            }
        }
        return response()->json($error);
    }

    public function annulationCommande(Request $request) {
        $lignecommande = ligneCommande::where('commande_id','=',$request->input('commande_id'))
                                        ->select('produit_id', 'quantite')
                                        ->get();
        $result = (array) $lignecommande;
        foreach ($result as $data) {

            $quantite = produit::where('id',$data['produit_id'])->first();

            $produit = produit::find($data['produit_id']);
            $produit->quantite = $quantite->quantite + $data['quantite'];
            $produit->save();
        }

        $commande = commande::find($request->input('commande_id'));
        $commande->etat_commande = "annuler";
        $commande->save();

        return response()->json(array('message' => 'commande annulée', 200));
    }

   public function listeCommande(){
        $commande = DB::table('commandes')
        ->join('etat_commandes','etat_commandes.id','commandes.etat_id')
        ->join('addresses','addresses.id','commandes.addresse_id')
        ->join('users','users.id','commandes.user_id')
        ->leftjoin('livraisons','livraisons.commande_id','=','commandes.id')
        ->leftjoin('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->where('commandes.etat_id','<>',3)
        ->select(
            'commandes.*',
            'commandes.etat_id as etat_commande_id',
            'commandes.id as id_commande',
            'addresses.*',
            'users.nom',
            'users.email',
            'users.pays_id',
            'users.type_id',
            'users.avatar',
            'livraisons.*',
            'etat_commandes.libelle_etat_commande as etat_commande',
            'etat_livraisons.libelle_etat_livraison as etat',
            'livraisons.etat_id as etat_livraison_id'
            )
        ->get();

        return response($commande);
    }

       public function listeproduitCommande(Request $request){

        $commande = DB::table('commandes')
        ->join('ligne_commandes','ligne_commandes.commande_id','commandes.id')
        ->join('produits','produits.id','ligne_commandes.produit_id')
        ->join('devises','devises.id','produits.devise_id')
        ->where('commandes.id',$request->input('key'))
        ->select(
            'ligne_commandes.prix',
            'ligne_commandes.quantite',
            'produits.libelle',
            'produits.image',
            'devises.symbole',
            'devises.position'
            )
        ->get();
        return response()->json($commande);
    }

    public function ChangeEtatCommande(Request $request){
        $commande = commande::find($request->input('id'));
        if ($request->input('etat') == 3) {

            $lignecommande = ligneCommande::where('commande_id','=',$request->input('id'))
                                        ->select('produit_id', 'quantite')
                                        ->get();


            foreach ($lignecommande as $data) {


                $quantite = produit::where('id',$data['produit_id'])->first();

                $produit = produit::find($data['produit_id']);
                $produit->quantite = $quantite->quantite + $data['quantite'];
                $produit->save();
            }

            $commande = commande::find($request->input('id'));
            $commande->etat_id =  3;
            $commande->save();

        } else {

            $commande->etat_id = $request->input('etat');
            $commande->save();

        }
        $date =  date('Y/m/d');
        $etat = etatCommande::find($request->input('etat'));
        $etat = $etat->libelle_etat_commande;
        return response()->json(array("success"=>"true","etat"=>$etat,'date'=>$date,200));
    }

    public function listeCommandeClient(Request $request){
        $commandes = DB::table('commandes')
        ->join('users','users.id','commandes.user_id')
        ->join('etat_commandes', 'etat_commandes.id', 'commandes.etat_id')
        ->where('users.id',$request->input('user_id'))
        ->select('commandes.*', 'etat_commandes.libelle_etat_commande as etat_commande')
        ->get();

        $commandeClient = [];

        foreach ($commandes as $commande) {
            $commandeClient[] = $commande;
            $records = ligneCommande::join('produits','produits.id','=','ligne_commandes.produit_id')
                ->where('ligne_commandes.commande_id',$commande->id)
                ->select(
                    'produits.libelle',
                    'produits.image',
                    'ligne_commandes.id',
                    'ligne_commandes.quantite',
                    'ligne_commandes.prix',
                    'ligne_commandes.produit_id'
                    )
                ->get();
            $commande->ligne_Commande = $records;
        }
        return response()->json($commandeClient);
    }


    public function listeCommandejourParEtat(Request $request){
        $dateday = $request->input('dateday');
        $etat = $request->input('etat_id');
        $commande = DB::table('commandes')
        ->join('etat_commandes','etat_commandes.id','commandes.etat_id')
        ->join('addresses','addresses.id','commandes.addresse_id')
        ->join('users','users.id','commandes.user_id')
        ->leftjoin('livraisons','livraisons.commande_id','=','commandes.id')
        ->leftjoin('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->where([
          ['commandes.date_commande',$dateday],
          ['commandes.etat_id',$etat],
        ])
        ->select(
            'commandes.*',
            'commandes.etat_id as etat_commande_id',
            'commandes.id as id_commande',
            'addresses.*',
            'users.nom',
            'users.email',
            'users.pays_id',
            'users.type_id',
            'users.avatar',
            'livraisons.*',
            'etat_commandes.libelle_etat_commande as etat_commande',
            'etat_livraisons.libelle_etat_livraison as etat',
            'livraisons.etat_id as etat_livraison_id'
            )
        ->limit(10)
        ->get();


        return response($commande);

    }


    public function listeCommandejourTotal(Request $request){
        $dateday = $request->input('dateday');
        $commande = DB::table('commandes')
        ->join('etat_commandes','etat_commandes.id','commandes.etat_id')
        ->join('addresses','addresses.id','commandes.addresse_id')
        ->join('users','users.id','commandes.user_id')
        ->leftjoin('livraisons','livraisons.commande_id','=','commandes.id')
        ->leftjoin('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->where('commandes.date_commande',$dateday)
        ->select(
            'commandes.*',
            'commandes.etat_id as etat_commande_id',
            'commandes.id as id_commande',
            'addresses.*',
            'users.nom',
            'users.email',
            'users.pays_id',
            'users.type_id',
            'users.avatar',
            'livraisons.*',
            'etat_commandes.libelle_etat_commande as etat_commande',
            'etat_livraisons.libelle_etat_livraison as etat',
            'livraisons.etat_id as etat_livraison_id'
            )
        ->limit(10)
        ->get();

        $nbrecommandetotal = commande::all()->count();
      return response($commande);

    }


    public function listeCommandeParEtat(Request $request){

      $etat = $request->input('etat_id');
      $commande = DB::table('commandes')
      ->join('etat_commandes','etat_commandes.id','commandes.etat_id')
      ->join('addresses','addresses.id','commandes.addresse_id')
      ->join('users','users.id','commandes.user_id')
      ->leftjoin('livraisons','livraisons.commande_id','=','commandes.id')
      ->leftjoin('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
      ->where('commandes.etat_id',$etat)
      ->select(
          'commandes.*',
          'commandes.etat_id as etat_commande_id',
          'commandes.id as id_commande',
          'addresses.*',
          'users.nom',
          'users.email',
          'users.pays_id',
          'users.type_id',
          'users.avatar',
          'livraisons.*',
          'etat_commandes.libelle_etat_commande as etat_commande',
          'etat_livraisons.libelle_etat_livraison as etat',
          'livraisons.etat_id as etat_livraison_id'
          )
      ->limit(10)
      ->get();


    return response($commande);

    }




    public function listecommandeSemaineParEtat(Request $request){
        $auj = $request->input('dateday') ;
        $t_auj = strtotime($auj);
        $p_auj = date('N', $t_auj);
        if ($p_auj == 1) {
            $deb = $t_auj;
            $fin = strtotime($auj . ' + 6 day');
        } else if ($p_auj == 7) {
            $deb = strtotime($auj . ' - 6 day');
            $fin = $t_auj;
        } else {
            $deb = strtotime($auj . ' - ' . (6 - (7 - $p_auj)) . ' day');
            $fin = strtotime($auj . ' + ' . (7 - $p_auj) . ' day');
        }
        $date_debut= strftime('%Y-%m-%d ', $deb).'<br />';
        $date_fin= strftime('%Y-%m-%d ', $fin).'<br />';

        $etat = $request->input('etat_id');
        $commande = DB::table('commandes')
        ->join('etat_commandes','etat_commandes.id','commandes.etat_id')
        ->join('addresses','addresses.id','commandes.addresse_id')
        ->join('users','users.id','commandes.user_id')
        ->leftjoin('livraisons','livraisons.commande_id','=','commandes.id')
        ->leftjoin('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->whereBetween('commandes.date_commande',[$date_debut,$date_fin])
        ->where('commandes.etat_id',$etat)
        ->select(
            'commandes.*',
            'commandes.etat_id as etat_commande_id',
            'commandes.id as id_commande',
            'addresses.*',
            'users.nom',
            'users.email',
            'users.pays_id',
            'users.type_id',
            'users.avatar',
            'livraisons.*',
            'etat_commandes.libelle_etat_commande as etat_commande',
            'etat_livraisons.libelle_etat_livraison as etat',
            'livraisons.etat_id as etat_livraison_id'
            )
        ->limit(10)
        ->get();

        $nbrecommandetotal = commande::all()->count();
      return response($commande);
    }


    public function listecommandeSemaineTous(Request $request){
        $auj = $request->input('dateday') ;
        $t_auj = strtotime($auj);
        $p_auj = date('N', $t_auj);
        if ($p_auj == 1) {
            $deb = $t_auj;
            $fin = strtotime($auj . ' + 6 day');
        } else if ($p_auj == 7) {
            $deb = strtotime($auj . ' - 6 day');
            $fin = $t_auj;
        } else {
            $deb = strtotime($auj . ' - ' . (6 - (7 - $p_auj)) . ' day');
            $fin = strtotime($auj . ' + ' . (7 - $p_auj) . ' day');
        }
        $date_debut= strftime('%Y-%m-%d ', $deb).'<br />';
        $date_fin= strftime('%Y-%m-%d ', $fin).'<br />';

        $commande = DB::table('commandes')
        ->join('etat_commandes','etat_commandes.id','commandes.etat_id')
        ->join('addresses','addresses.id','commandes.addresse_id')
        ->join('users','users.id','commandes.user_id')
        ->leftjoin('livraisons','livraisons.commande_id','=','commandes.id')
        ->leftjoin('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->whereBetween('commandes.date_commande',[$date_debut,$date_fin])
        ->select(
            'commandes.*',
            'commandes.etat_id as etat_commande_id',
            'commandes.id as id_commande',
            'addresses.*',
            'users.nom',
            'users.email',
            'users.pays_id',
            'users.type_id',
            'users.avatar',
            'livraisons.*',
            'etat_commandes.libelle_etat_commande as etat_commande',
            'etat_livraisons.libelle_etat_livraison as etat',
            'livraisons.etat_id as etat_livraison_id'
            )
        ->limit(10)
        ->get();


      return response($commande);
    }



    public function listecommande_moisParEtat(Request $request){
        $mois=date('m',strtotime($request->input('dateday')));
        $annee=date('Y',strtotime($request->input('dateday')));

        $etat = $request->input('etat_id');
        $commande = DB::table('commandes')
        ->join('etat_commandes','etat_commandes.id','commandes.etat_id')
        ->join('addresses','addresses.id','commandes.addresse_id')
        ->join('users','users.id','commandes.user_id')
        ->leftjoin('livraisons','livraisons.commande_id','=','commandes.id')
        ->leftjoin('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->whereYear('commandes.date_commande',$annee)
        ->whereMonth('commandes.date_commande',$mois)
        ->where('commandes.etat_id',$etat)
        ->select(
            'commandes.*',
            'commandes.etat_id as etat_commande_id',
            'commandes.id as id_commande',
            'addresses.*',
            'users.nom',
            'users.email',
            'users.pays_id',
            'users.type_id',
            'users.avatar',
            'livraisons.*',
            'etat_commandes.libelle_etat_commande as etat_commande',
            'etat_livraisons.libelle_etat_livraison as etat',
            'livraisons.etat_id as etat_livraison_id'
            )
        ->limit(10)
        ->get();
      return response($commande);
    }



    public function listecommande_moisTotal(Request $request){
        $mois=date('m',strtotime($request->input('dateday')));
        $annee=date('Y',strtotime($request->input('dateday')));

        $commande = DB::table('commandes')
        ->join('etat_commandes','etat_commandes.id','commandes.etat_id')
        ->join('addresses','addresses.id','commandes.addresse_id')
        ->join('users','users.id','commandes.user_id')
        ->leftjoin('livraisons','livraisons.commande_id','=','commandes.id')
        ->leftjoin('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->whereYear('commandes.date_commande',$annee)
        ->whereMonth('commandes.date_commande',$mois)
        ->select(
            'commandes.*',
            'commandes.etat_id as etat_commande_id',
            'commandes.id as id_commande',
            'addresses.*',
            'users.nom',
            'users.email',
            'users.pays_id',
            'users.type_id',
            'users.avatar',
            'livraisons.*',
            'etat_commandes.libelle_etat_commande as etat_commande',
            'etat_livraisons.libelle_etat_livraison as etat',
            'livraisons.etat_id as etat_livraison_id'
            )
        ->limit(10)
        ->get();

      return response($commande);
    }



}
