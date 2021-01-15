<?php

namespace App\Http\Controllers;

use App\commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentaireController extends Controller
{

   public function enregistrerCommentaire(Request $request){
    $datenow = date('Y/m/d');

    $validateData = $request->validate([
        'comment_id' => 'nullable|integer',
        'produit_id' => 'nullable|integer',
        'user_id' => 'required|integer',
        'comment' => 'required',
    ]);

    $commentaire = new commentaire();
    $commentaire->produit_id = $request->produit_id;
    $commentaire->user_id = $request->user_id;
    $commentaire->comment = $request->comment;
    $commentaire->date_comment = $datenow;

    $commentaire->save();

    if($commentaire->save()){
        return response()->json(array('success'=>true,200));
    }
    return response()->json(array('success'=>false,400));
   }



   public function afficheCommentaireProduit(Request $request){
        $commentaire = DB::table('commentaires')
            ->leftjoin('users','users.id','commentaires.user_id')
            ->where('commentaires.produit_id',$request->key)
            ->select(
                'commentaires.*',
                'users.nom',
                'users.email',
                'users.avatar'
                )
            ->get();
            return response()->json($commentaire);
        }

//    public function afficheCommentaireCommentaire(Request $request){
//         $commentaire = DB::table('commentaires')
//             ->leftjoin('clients','clients.id','commentaires.client_id')
//             ->where('commentaires.commentaire_id',$request->input('id'))
//             ->select('commentaires.*','clients.*')
//             ->get();
//             return response()->json($commentaire);
//         }

   public function supprimerCommentaire(Request $request){

        $id = $request->input('id');

        $commentaire = commentaire::where('id',$id)->delete();
        if($commentaire){
            return response()->json(array('success'=>true,200));
        }
         return response()->json(array('success'=>false,400));
   }
}
