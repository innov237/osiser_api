<?php

namespace App\Http\Controllers;

use App\categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategorieController extends Controller
{
    public function ajouterCategorie(Request $request){
        $request->validate([
            'libelle' => 'required|string|unique:categories',
            'description' => 'nullable|string',
            'id_categorie_parent' => 'nullable|integer',
            'image' => 'nullable',
        ]);
        $categorie = new categorie;
        $categorie->libelle = $request->libelle;
        $categorie->description = $request->description;
        $categorie->id_categorie_parent = $request->id_categorie_parent;
        $categorie->img = $request->image;
        $categorie->save();
        return response()->json(array('message'=>'categorie enregistrée','success'=>true));
    }

    public function listeCategorie(){
        $categorie = categorie::where([['id_categorie_parent','=',NULL],['est_actif','=',1]])->get();
        return response()->json($categorie);
    }

    public function ProduitlisteSousCategorie(Request $request){
        $sousCategorie = DB::table('categories as c1')
                                    ->join('categories as c2','c1.id','=', 'c2.id_categorie_parent')
                                    ->Leftjoin('produits', 'c2.id', '=', 'produits.categorie_id')
                                    ->where([['c1.id','=',$request->input('id_categorie_parent')],['c1.est_actif','=',1]])
                                    ->select('c2.libelle as categorie','produits.libelle')
                                    ->get();
        return response()->json($sousCategorie);
    }

    public function listeSousCategorie(Request $request)
    {
        $sousCategorie = DB::table('categories as c1')
            ->join('categories as c2', 'c1.id', '=', 'c2.id_categorie_parent')
            ->where([['c1.id', '=', $request->input('key')], ['c1.est_actif', '=', 1], ['c2.est_actif', '=', 1]])
            ->select('c2.*')
            ->get();
        return response()->json($sousCategorie);
    }

    public function listeCategorieParent(Request $request)
    {
        $categorieParent = [];
        $categorie = DB::table('categories as c1')
            ->join('categories as c2', 'c1.id', '=', 'c2.id_categorie_parent')
            ->where([['c2.id', '=', $request->input('key')], ['c1.est_actif', '=', 1], ['c2.est_actif', '=', 1]])
            ->select('c2.*')
            ->first();
            array_push($categorieParent,$categorie);
            while ($categorie->id_categorie_parent) {
                $categorie = categorie::where('id',$categorie->id_categorie_parent)->first();
                array_push($categorieParent,$categorie);
            }
       
        return response()->json($categorieParent);
    }

    public function modifierCategorie(Request $request){
         $request->validate([
            'libelle' => 'required|string|unique:categories,libelle,'.$request->input('id'),
            'description' => 'nullable|string',
            'id_categorie_parent' => 'nullable|integer',
            'image' => ''
        ]);
        $categorie = categorie::find($request->input('id'));
        $categorie->libelle = $request->libelle;
        $categorie->description = $request->description;
        $categorie->id_categorie_parent = $request->id_categorie_parent;
        $categorie->img = $request->image;
        $categorie->save();
        return response()->json(array('message'=>'categorie modifiée',200));
    }

     public function supprimeCategorie(Request $request){

        $categorie = categorie::find($request->input('id'));
        $categorie->est_actif = 0;
        $categorie->save();
        return response()->json(array('message'=>'categorie supprimée',200));
    }

    public function categorieGroupee()
    {
        $categorie = categorie::groupeCategorie();
        return $categorie;
    }

    public function telechargerImageCategorie(Request $request)
    {
        if ($request->hasFile('image')) {

            $files = $request->file('image');
            $request->image->move('storage/', $files->getClientOriginalName());
            return response()->json(array('message' => 'image upload success'));
        }
        return response()->json(array('message' => 'image upload error'));
    }
}
