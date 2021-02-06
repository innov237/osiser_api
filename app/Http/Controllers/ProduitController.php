<?php

namespace App\Http\Controllers;

use App\produit;
use App\historiqueStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Validator;
use Illuminate\Validation\Rule;

class ProduitController extends Controller
{
    
    public function getAllpromotion() {
        $produits = DB::table('produits')
            ->join('devises', 'devises.id', '=', 'produits.devise_id')
            ->select(
                'produits.*',
                'devises.symbole',
                'devises.id as devise_id'
                )
            ->where([['produits.est_actif', 1],['produits.is_promo',true]])
            ->get();
        $produitComment=[];
        foreach ($produits as $produit) {
            $produitComment[] = $produit;
            $records = DB::table('commentaires')
                    ->join('produits', 'commentaires.produit_id', '=', 'produits.id')
                    ->join('users', 'users.id', '=', 'commentaires.user_id')
                    ->where('commentaires.produit_id',$produit->id)
                    ->select(
                        'commentaires.comment',
                        'commentaires.id as id_commentaire',
                        'commentaires.date_comment',
                        'users.nom',
                        'users.email',
                        'users.avatar',
                        'users.telephone'
                        )
                    ->get();
            $produit->comments = $records;
        }
        return response()->json($produitComment);
    }

    public function listeProduit()
    {

        $produits = DB::table('produits')
            ->join('devises', 'devises.id', '=', 'produits.devise_id')
            ->select(
                'produits.*',
                'devises.symbole',
                'devises.id as devise_id'
                )
            ->where('produits.est_actif', 1)
            ->get();
        $produitComment=[];
        foreach ($produits as $produit) {
            $produitComment[] = $produit;
            $records = DB::table('commentaires')
                    ->join('produits', 'commentaires.produit_id', '=', 'produits.id')
                    ->join('users', 'users.id', '=', 'commentaires.user_id')
                    ->where('commentaires.produit_id',$produit->id)
                    ->select(
                        'commentaires.comment',
                        'commentaires.id as id_commentaire',
                        'commentaires.date_comment',
                        'users.nom',
                        'users.email',
                        'users.avatar',
                        'users.telephone'
                        )
                    ->get();
            $produit->comments = $records;
        }
        return response()->json($produitComment);
    }

    public function listeProduitSupprimer()
    {
        $produit = DB::table('produits')
            // ->join('categories', 'categories.id', '=', 'produits.categorie_id')
            ->join('devises', 'devises.id', '=', 'produits.devise_id')
            ->select(
                'produits.*',
                'devises.symbole',
                'devises.id as devise_id')
            ->where('produits.est_actif', 0)
            //->limit(15)
            ->get();
        return response()->json($produit);
    }


    public function produitParCategories(Request $request)
    {
        $id = $request->input('key');
        $produit = DB::table('produits')
            ->join('categories', 'categories.id', '=', 'produits.categorie_id')
            ->join('devises', 'devises.id', '=', 'produits.devise_id')
            ->where([
                ['produits.est_actif', 1],
                ['produits.categorie_id', $id]
            ])
            ->select(
                'produits.*',
                'devises.symbole'
            )
            ->get();
        return response()->json($produit);
    }


    public function enregistrerProduit(Request $request)
    {

            $request->validate([
                'devise_id' => 'required|integer',
                'libelle' => 'required|unique:produits',
                'prix_achat' => 'required|numeric',
                'prix_vente' => 'required|numeric',
                'description' => 'nullable',
                'categorie_id' => 'required|integer',
                'quantite' => 'required|integer',
                'keyword' => '',
                'note' => '',
                'reduction'=>'',
                'image' => 'nullable',
                'user_id'=>'required'
            ]);
            $produit = new produit;
            $produit->devise_id = $request->devise_id;
            $produit->libelle = $request->libelle;
            $produit->prix_achat = $request->prix_achat;
            $produit->prix_vente = $request->prix_vente;
            $produit->description = $request->description;
            $produit->categorie_id = $request->categorie_id;
            $produit->quantite = $request->quantite;
            $produit->keyword = $request->keyword;
            $produit->note = $request->note;
            $produit->reduction = $request->reduction;
            $produit->user_id = $request->user_id;
            $produit->slug =  preg_replace('/\s+/', '_', $request->libelle)."_".rand(0,100000)."_".preg_replace('/\s+/', '_', $request->categorie_id);
            $produit->image = json_encode($request->image);

            $produit->save();
            $id_produit = $produit->getKey();
                // historique de chaque article
            $historique = new historiqueStock();
            $historique->devise_id = $request->devise_id;
            $historique->produit_id = $id_produit;
            $historique->categorie_id = $request->categorie_id;
            $historique->prix_achat = $request->prix_achat;
            $historique->prix_vente = $request->prix_vente;
            $historique->description = $request->description;
            $historique->quantite = $request->quantite;
            $historique->libelle = $request->libelle;
            $historique->keyword = $request->keyword;
            $historique->user_id = $request->user_id;

            $historique->save();


        return response()->json(array('message'=>'Produit enregistre avec success',"success" => true, 200));
    }

    public function sulgProduit() {
        $list = produit::all();

        foreach($list as $key){
            produit::where('id', $key->id)
              ->update(['slug' => preg_replace('/\s+/', '_', $key->libelle)."_".rand(0,100000)."_".preg_replace('/\s+/', '_', $key->categorie_id)]);
        }

        return response()->json('success');
    }

     public function approvisionnerProduit(Request $request)
    {

            $request->validate([
                'devise_id' => 'required|integer',
                'libelle' => 'required',
                'prix_achat' => 'required|numeric',
                'prix_vente' => 'required|numeric',
                'description' => 'nullable',
                'categorie_id' => 'required|integer',
                'quantite' => 'required|integer',
                'keyword' => '',
                'id'=> '',
                'user_id'=>'required'
            ]);

            $produit = produit::find($request->id);
            $produit->prix_achat = $request->prix_achat;
            $produit->prix_vente = $request->prix_vente;
            $produit->quantite = $request->quantite + $produit->quantite;
            $produit->user_id = $request->user_id;

            $produit->save();
                // historique de chaque article
            $historique = new historiqueStock();
            $historique->devise_id = $request->devise_id;
            $historique->produit_id = $request->id;
            $historique->categorie_id = $request->categorie_id;
            $historique->prix_achat = $request->prix_achat;
            $historique->prix_vente = $request->prix_vente;
            $historique->description = $request->description;
            $historique->quantite = $request->quantite;
            $historique->libelle = $request->libelle;
            $historique->keyword = $request->keyword;
            $historique->user_id = $request->user_id;

            $historique->save();


        return response()->json(array('message'=>'Approvisionnement du produit '.$historique->libelle.' effectué',"success" => true, 200));
    }

    public function telechargerImage(Request $request)
    {
        if ($request->hasFile('image')) {

            $files = $request->file('image');
            $request->image->move('storage/', $files->getClientOriginalName());
            return response()->json(array('message' => 'image upload success'));
        }
        return response()->json(array('message' => 'image upload error'));
    }


    public function rechercherProduit(Request $request)
    {
        $key = $request->input('key');
        $produit = DB::table('produits')
            ->join('categories', 'categories.id', '=', 'produits.categorie_id')
            ->leftjoin('devises', 'devises.id', '=', 'produits.devise_id')
            ->select(
                'produits.*',
                'devises.symbole',
                'devises.id as devise_id'
                // 'produits.id as produit_id',
                // 'produits.libelle as libelleProduit',
                // 'categories.*',
                // 'categories.libelle as libelleCategorie',
                // 'devises.*',
                // 'devises.libelle as libelleDevise'
                )
            ->where([
                ['produits.est_actif', 1],
                ['produits.keyword', 'like', '%' . $key . '%']
            ])
            ->get();
        return response()->json($produit);
    }


    public function modifierProduit(Request $request)
    {
        $id = $request->input('id');
        $request->validate([
            'devise_id' => 'integer',
            'libelle' => 'required',
            'prix_achat' => 'required|numeric',
            'prix_vente' => 'required|numeric',
            'description' => 'nullable',
            'categorie_id' => 'integer',
            'image' => 'nullable',
            'keyword' => 'required'
        ]);

        $produit = produit::find($id);
        $produit->libelle = $request->libelle;
        $produit->prix_achat = $request->prix_achat;
        $produit->prix_vente = $request->prix_vente;
        $produit->description = $request->description;
        $produit->quantite = $request->quantite;
        $produit->keyword = $request->keyword;
        if ($request->image){
            $produit->image = json_encode($request->image);
        }

        $produit->save();
        if ($produit) {
            return response()->json(array('success' => true, 200));
        }
        return response()->json(array('success' => false, 400));
    }


    public function SupprimerProduit(Request $request)
    {
        $id = $request->input('id');
        $produit = DB::table('produits')
            ->where('id', $id)
            ->update([
                'est_actif' => 0,
            ]);
        if ($produit) {
            return response()->json(array('success' => true, 200));
        }
        return response()->json(array('success' => false, 400));
    }

    public function alertProduit(){
        $produit = produit::where([['quantite','=', 5],['est_actif','=','1']])
                            ->get();
        return  response()->json($produit);
    }

    public function ruptureProduit(){
        $produit = produit::where([['quantite','=', 0],['est_actif','=','1']])
                            ->get();
        return  response()->json($produit);
    }

     public function rechercheProduitParId(Request $request){
        $id = $request->input('key');
        $produit = DB::table('produits')->leftjoin('devises','devises.id','produits.devise_id')
        ->where([
            ['produits.id',$id],
            ['produits.est_actif',1]
        ])
        ->select("produits.*","devises.symbole","devises.position")
        ->first();
        return response()->json($produit);
    }
    
    public function produitBySlug(Request $request){
        $produit = produit::join('devises', 'devises.id', '=', 'produits.devise_id')
        ->select(
            'produits.*',
            'devises.symbole',
            'devises.id as devise_id'
            )
        ->where('slug',$request->key)
        ->first();
        $produitComment;
        $records = DB::table('commentaires')
                ->join('produits', 'commentaires.produit_id', '=', 'produits.id')
                ->join('users', 'users.id', '=', 'commentaires.user_id')
                ->where('commentaires.produit_id',$produit->id)
                ->select(
                    'commentaires.comment',
                    'commentaires.id as id_commentaire',
                    'commentaires.date_comment',
                    'users.nom',
                    'users.email',
                    'users.avatar',
                    'users.telephone'
                    )
                ->get();
        $produit->comments = $records;
        $produitComment = $produit;

        return response()->json($produitComment);
    }

    public function ajouterEnPromotion(Request $request){
        produit::where('id',$request->id)
                ->update(['is_promo'=>true]);
        return response()->json(array('message' => 'mise en vente flash','success'=>true));
    }
    public function retirerEnPromotion(Request $request){
        produit::where('id',$request->id)
                ->update(['is_promo'=>false]);
        return response()->json(array('message' => 'mise en vente flash','success'=>true));
    }

    public function update(Request $request, $id){
        //

        $credentials = Validator::make($request->all(),[
            'reduction' => 'required|numeric'
        ]);

        if ($credentials->fails()) {
            return response()->json(
                array(
                    'success'=>false,
                    'message' => 'UNPROCESS',
                    'detail'=>$credentials->errors()
                ),422
            );
        }

        $produit = produit::find($id);

        if ($produit){
            $produit->update($request->only(['reduction']));
            return response()->json(array('success' => true), 200);
        }
        return response()->json(
                array(
                    'success'=>false,
                    'message' => 'NOT FOUND',
                    'detail'=> "Not found boutique with id ".$id
                ),404
            );

    }
}
