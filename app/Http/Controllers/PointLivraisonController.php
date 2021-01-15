<?php

namespace App\Http\Controllers;

use App\point_livraison;
use App\ville;
use App\pays;
use Illuminate\Http\Request;

class PointLivraisonController extends Controller
{
  public function creerpointlivraison(Request $request){
    $validateData = $request->validate([
        'pays' => 'required',
        'ville' => 'required',
        'rue' => 'required|string',
        'Description' => 'required',
    ]);
    $point_livraison = point_livraison::create($validateData);
    return response(['success'=>true,'message'=>'Enregistré avec succès']);
  }

  public function listepointlivraison()
  {
      return point_livraison::all();
  }
}
