<?php

namespace App\Http\Controllers;

use App\livraison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LivraisonController extends Controller
{

  public function creerLivraison(Request $request){

    $validateData = $request->validate([
        'id' => 'required|integer|unique:livraisons',
        'livreur_id' => 'required|integer',
        'date_livraison' => 'required|date',
    ]);
    $livraison = new livraison;
    $livraison->commande_id = $request->input('id');
    $livraison->user_id = $request->input('livreur_id');
    $livraison->etat_id = 1;
    $livraison->date_livraison = $request->input('date_livraison');
    $livraison->save();

    if($livraison){
        $id = $livraison->getKey();
        $mylivraison = livraison::find($id);
        return response()->json(array('success'=>true,'livraison'=>$mylivraison,200));
    }
    return response()->json(array('success'=>false,400));
  }

  public function livraison_commande(Request $request){
    $livraison = livraison::join('users','users.id','livraisons.user_id')->join('type_users','type_users.id','users.type_id')
    ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
    ->where([
        ['commande_id',$request->input('key')],
        ['type_users.libelleType','livreur']
        ])->select('users.nom','livraisons.*','etat_livraisons.libelle_etat_livraison as etat')->get();
      return response()->json($livraison);
  }

    public function listeLivraison(){
        $livraison = DB::table('livraisons')
        ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->join('commandes','commandes.id','livraisons.commande_id')
        ->join('addresses','commandes.addresse_id','addresses.id')
        ->join('users','users.id','livraisons.user_id')
        // ->where('users.type_id',3)
        ->select('livraisons.*','etat_livraisons.*','livraisons.id as id_livraison' ,'addresses.*','addresses.pays as pays_livraison','users.nom')
        ->limit(10)
        ->get();

        $nbrelivraisontotal = livraison::all()->count();
        $nbrelivraisoneffectue = livraison::where('livraisons.etat_id',2)->count();
        $nbrelivraisonencour = livraison::where('livraisons.etat_id',1)->count();
        $nbrelivraisonretourne = livraison::where('livraisons.etat_id',3)->count();
        return response([
            'livraison'=>$livraison,
            'nbrelivraisontotal'=>$nbrelivraisontotal,
            'nbrelivraisonencour'=>$nbrelivraisonencour,
            'nbrelivraisonretourne'=>$nbrelivraisonretourne,
            'nbrelivraisoneffectue'=>$nbrelivraisoneffectue
        ]);
    }

    public function listeLivraisonParEtat(Request $request){
        $livraison = DB::table('livraisons')
        ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->join('commandes','commandes.id','livraisons.commande_id')
        ->join('addresses','commandes.addresse_id','addresses.id')
        ->join('users','users.id','livraisons.user_id')
        ->where('livraisons.etat_id',$request->input('etat_id'))
        ->select('livraisons.*','etat_livraisons.*','livraisons.id as id_livraison' ,'addresses.*','addresses.pays as pays_livraison','users.nom')
        ->limit(10)
        ->get();

        return response()->json($livraison);
    }

    public function ChangeEtatlivraison(Request $request){
        $livraison = livraison::find($request->input('id'));
        $livraison->etat_id = $request->input('etat');
        $livraison->save();
        return response()->json(array("success"=>"true",200));
    }


    public function annulerLivraison(Request $request){
        $id = $request->input('id');
       DB::table('livraisons')->where('id', '=', $id)->delete();
       return response()->json(array('success'=>true,200));
    }

    public function listelivraisonjourTotal(Request $request){
        $dateday = $request->input('dateday');
        $livraison = DB::table('livraisons')
        ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->join('commandes','commandes.id','livraisons.commande_id')
        ->join('addresses','commandes.addresse_id','addresses.id')
        ->join('users','users.id','livraisons.user_id')
        ->where('livraisons.date_livraison',$dateday)
        ->select('livraisons.*','etat_livraisons.*','users.nom','livraisons.id as id_livraison' ,'addresses.*','addresses.pays as pays_livraison')
        ->limit(10)
        ->get();

        $nbrelivraisontotal = livraison::all()->count();
        $nbrelivraisoneffectue = livraison::where('livraisons.etat_id',2)->count();
        $nbrelivraisonencour = livraison::where('livraisons.etat_id',1)->count();
        $nbrelivraisonretourne = livraison::where('livraisons.etat_id',3)->count();
        return response([
            'livraison'=>$livraison,
            'nbrelivraisontotal'=>$nbrelivraisontotal,
            'nbrelivraisonencour'=>$nbrelivraisonencour,
            'nbrelivraisonretourne'=>$nbrelivraisonretourne,
            'nbrelivraisoneffectue'=>$nbrelivraisoneffectue
        ]);

    }

    public function listelivraisonjourParEtat(Request $request){
        $dateday = $request->input('dateday');

        $livraison =  DB::table('livraisons')
        ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->join('commandes','commandes.id','livraisons.commande_id')
        ->join('addresses','commandes.addresse_id','addresses.id')
        ->join('users','users.id','livraisons.user_id')
        ->where([
            ['date_livraison',$dateday],
            ['livraisons.etat_id', $request->input('etat_id')]
        ])
        ->select('livraisons.*','etat_livraisons.*','users.nom','livraisons.id as id_livraison' ,'addresses.*','addresses.pays as pays_livraison')
        ->limit(10)
        ->get();

        $nbrelivraisontotal = livraison::all()->count();
        $nbrelivraisoneffectue = livraison::where('livraisons.etat_id',2)->count();
        $nbrelivraisonencour = livraison::where('livraisons.etat_id',1)->count();
        $nbrelivraisonretourne = livraison::where('livraisons.etat_id',3)->count();
        return response([
            'livraison'=>$livraison,
            'nbrelivraisontotal'=>$nbrelivraisontotal,
            'nbrelivraisonencour'=>$nbrelivraisonencour,
            'nbrelivraisonretourne'=>$nbrelivraisonretourne,
            'nbrelivraisoneffectue'=>$nbrelivraisoneffectue
        ]);

    }

    public function listeLivraisonSemaine(Request $request){
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

        $livraison =  DB::table('livraisons')
        ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->join('commandes','commandes.id','livraisons.commande_id')
        ->join('addresses','commandes.addresse_id','addresses.id')
        ->join('users','users.id','livraisons.user_id')
        ->whereBetween('livraisons.date_livraison',[$date_debut,$date_fin])
        ->select('livraisons.*','etat_livraisons.*','users.nom','livraisons.id as id_livraison' ,'addresses.*','addresses.pays as pays_livraison')
        ->limit(10)
        ->get();

        $nbrelivraisontotal = livraison::all()->count();
        $nbrelivraisoneffectue = livraison::where('livraisons.etat_id',2)->count();
        $nbrelivraisonencour = livraison::where('livraisons.etat_id',1)->count();
        $nbrelivraisonretourne = livraison::where('livraisons.etat_id',3)->count();
        return response([
            'livraison'=>$livraison,
            'nbrelivraisontotal'=>$nbrelivraisontotal,
            'nbrelivraisonencour'=>$nbrelivraisonencour,
            'nbrelivraisonretourne'=>$nbrelivraisonretourne,
            'nbrelivraisoneffectue'=>$nbrelivraisoneffectue
        ]);
    }


    public function listeLivraisonSemaineParEtat(Request $request){
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

        $livraison =  DB::table('livraisons')
        ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->join('commandes','commandes.id','livraisons.commande_id')
        ->join('addresses','commandes.addresse_id','addresses.id')
        ->join('users','users.id','livraisons.user_id')
        ->whereBetween('livraisons.date_livraison',[$date_debut,$date_fin])
        ->where('livraisons.etat_id', $request->input('etat_id'))
        ->select('livraisons.*','etat_livraisons.*','users.nom','livraisons.id as id_livraison' ,'addresses.*','addresses.pays as pays_livraison')
        ->limit(10)
        ->get();

        $nbrelivraisontotal = livraison::all()->count();
        $nbrelivraisoneffectue = livraison::where('livraisons.etat_id',2)->count();
        $nbrelivraisonencour = livraison::where('livraisons.etat_id',1)->count();
        $nbrelivraisonretourne = livraison::where('livraisons.etat_id',3)->count();
        return response([
            'livraison'=>$livraison,
            'nbrelivraisontotal'=>$nbrelivraisontotal,
            'nbrelivraisonencour'=>$nbrelivraisonencour,
            'nbrelivraisonretourne'=>$nbrelivraisonretourne,
            'nbrelivraisoneffectue'=>$nbrelivraisoneffectue
        ]);
    }


    public function livraison_mois(Request $request){
        $mois=date('m',strtotime($request->input('dateday')));
        $annee=date('Y',strtotime($request->input('dateday')));

        $livraison =  DB::table('livraisons')
        ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->join('commandes','commandes.id','livraisons.commande_id')
        ->join('addresses','commandes.addresse_id','addresses.id')
        ->join('users','users.id','livraisons.user_id')
        ->whereYear('livraisons.date_livraison',$annee)
        ->whereMonth('livraisons.date_livraison',$mois)
        ->select('livraisons.*','etat_livraisons.*','users.nom','livraisons.id as id_livraison' ,'addresses.*','addresses.pays as pays_livraison')
        ->limit(10)
        ->get();

        $nbrelivraisontotal = livraison::all()->count();
        $nbrelivraisoneffectue = livraison::where('livraisons.etat_id',2)->count();
        $nbrelivraisonencour = livraison::where('livraisons.etat_id',1)->count();
        $nbrelivraisonretourne = livraison::where('livraisons.etat_id',3)->count();
        return response([
            'livraison'=>$livraison,
            'nbrelivraisontotal'=>$nbrelivraisontotal,
            'nbrelivraisonencour'=>$nbrelivraisonencour,
            'nbrelivraisonretourne'=>$nbrelivraisonretourne,
            'nbrelivraisoneffectue'=>$nbrelivraisoneffectue
        ]);
    }

    public function livraison_moisParEtat(Request $request){
        $mois=date('m',strtotime($request->input('dateday')));
        $annee=date('Y',strtotime($request->input('dateday')));

        $livraison =  DB::table('livraisons')
        ->join('etat_livraisons','etat_livraisons.id','livraisons.etat_id')
        ->join('commandes','commandes.id','livraisons.commande_id')
        ->join('addresses','commandes.addresse_id','addresses.id')
        ->join('users','users.id','livraisons.user_id')
        ->whereYear('livraisons.date_livraison',$annee)
        ->whereMonth('livraisons.date_livraison',$mois)
        ->where('livraisons.etat_id', $request->input('etat_id'))
        ->select('livraisons.*','etat_livraisons.*','users.nom','livraisons.id as id_livraison' ,'addresses.*','addresses.pays as pays_livraison')
        ->limit(10)
        ->get();

        $nbrelivraisontotal = livraison::all()->count();
        $nbrelivraisoneffectue = livraison::where('livraisons.etat_id',2)->count();
        $nbrelivraisonencour = livraison::where('livraisons.etat_id',1)->count();
        $nbrelivraisonretourne = livraison::where('livraisons.etat_id',3)->count();
        return response([
            'livraison'=>$livraison,
            'nbrelivraisontotal'=>$nbrelivraisontotal,
            'nbrelivraisonencour'=>$nbrelivraisonencour,
            'nbrelivraisonretourne'=>$nbrelivraisonretourne,
            'nbrelivraisoneffectue'=>$nbrelivraisoneffectue
        ]);
    }
}
