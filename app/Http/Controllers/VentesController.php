<?php

namespace App\Http\Controllers;

use App\ventes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentesController extends Controller
{
    public function Enregistrer_vente(Request $request)
    {

        $datenow = date('Y/m/d');
        $validateData = $request->validate([
            'id' => 'required|integer|unique:ventes',
        ]);
        $vente = ventes::create([
            'commande_id' => $request->id,
            'date_vente' => $datenow
        ]);
        if ($vente) {
            return response()->json(array('success' => true, 200));
        }
        return response()->json(array('success' => false, 400));
    }


    public function annuler_vente(Request $request)
    {
        $id = $request->input('id');
        $vente = ventes::where('id', $id)->update([
            'est_actif' => 0
        ]);
        if ($vente) {
            return response()->json(array('success' => true, 200));
        }
        return response()->json(array('success' => false, 400));
    }

    public function afficher_vente()
    {
        $vente = DB::table('ventes')
            ->join('commandes', 'commandes.id', 'ventes.commande_id')
            ->join('clients', 'clients.id', 'commandes.client_id')
            ->join('ligne_commandes', 'ligne_commandes.commande_id', 'commandes.id')
            ->join('produits', 'produits.id', 'ligne_commandes.produit_id')
            ->select('ventes.*', 'commandes.*', 'clients.*', 'ligne_commandes.*', 'produits.*')
            ->where('ventes.est_actif', 1)
            ->limit(10)
            ->get();

        return response()->json($vente);
    }
}
