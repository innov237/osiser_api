<?php

namespace App\Http\Controllers;

use App\address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
   public function ajouterCoordonnee(Request $request){

       $request->validate([
            'user_id' => 'required',
            'nom' => 'required|string',
            'telephone' => 'required',
            'telephone2' => '',
            'pays' => 'required|string',
            'ville' => 'required|string',
            'rue' => 'required|string',
            'isDefault'=>'',
        ]);

        $address = new address();
        $address->user_id = $request->user_id;
        $address->noms = $request->nom;
        $address->telephone1 = $request->telephone;
        $address->telephone2 = $request->telephone2;
        $address->pays = $request->pays;
        $address->ville = $request->ville;
        $address->rue = $request->rue;
        $count = address::where('user_id',$request->user_id)->count();
        if ($count == 0){
            $address->isDefault = 1;
        }
        else if ($request->isDefault == 1){
            address::where([['isDefault',1],['user_id',$request->user_id]])->update(['isDefault' => 0]);
            $address->isDefault = 1;
        }else {
            $address->isDefault = 0 ;
        }

        $address->save();
        $address_id = $address->getKey();
        $adresseDefault = address::where('isDefault',1)->first();

        return response()->json(array('message' =>'addresse de livraison prise en compte','adresse'=>$adresseDefault,'adresse_id'=>$address_id ,'success'=>true, 201 ));
   }

   public function listeAdresseClient(Request $request){
        $adresseDefault = address::where(
            [
                ['isDefault',1],
                ['user_id',$request->input('key')]
            ])
            ->get();
        $adresse = address::where(
            [
                ['isDefault',0],
                ['user_id',$request->input('key')]
            ])
            ->get();
        return response()->json(array('otherAdresse'=>$adresse,'defaultAdresse'=>$adresseDefault));
   }

   public function changeDefaultAdresse(Request $request){
    //    return $request;
       address::where(
           [
               ['isDefault','=',1],
               ['user_id',$request->input('user_id')]
            ]
        )->update(['isDefault' => false]);
       address::where([['id',$request->input('adresse_id')],['user_id',$request->input('user_id')]])->update(['isDefault' => 1]);
        return response()->json(array('success'=>true, 201 ));
   }

   public function supprimeDefaultAdresse(Request $request){
    //    return $request;
       address::where(
           [
               ['isDefault','=',1],
               ['user_id',$request->input('user_id')]
            ]
        )->delete();
        $count = address::where('user_id',$request->input('user_id'))->count();
        if($count > 0){
          $adress = address::where('user_id',$request->input('user_id'))->first();
           address::where('id',$adress->id)->update(['isDefault' => 1]);
           $otheradress = address::where([['user_id',$request->input('user_id')],['isDefault',0]])->get();
             return response()->json(array('success'=>true, 201,'message'=>'Supprimé avec success','defaultAdresse'=>$adress, 'otherAdresse'=>$otheradress ));
        }else{
           return response()->json(array('success'=>true, 201,'message'=>'Supprimé avec success'));
        }


   }

   public function modifierAdresse(Request $request){
       $request->validate([
            'user_id' => 'required',
            'nom' => 'required|string',
            'telephone' => 'required',
            'telephone2' => '',
            'ville' => 'required|string',
            'rue' => 'required|string',
            'isDefault'=>'',
        ]);

        $address = address::find($request->input('adresse_id'));
        $address->user_id = $request->user_id;
        $address->noms = $request->nom;
        $address->telephone1 = $request->telephone;
        $address->telephone2 = $request->telephone2;
        $address->ville = $request->ville;
        $address->rue = $request->rue;

        if ($request->isDefault == true){
            address::where([['isDefault',1],['user_id',$request->user_id]])->update(['isDefault' => 0]);
            $address->isDefault = $request->isDefault;
        }else {
            $address->isDefault = $request->isDefault;
        }

        $address->save();
        $address_id = $address->getKey();
        $adresseDefault = address::where('id',$address_id)->get();

        return response()->json(array('message' =>'Modification d\'addresse de livraison prise en compte','adresse'=>$adresseDefault, 'success'=>true, 201 ));
   }

   public function supprimerAdresse(Request $request){
      // DB::table('addresses')->where('id', '=', $request->input('id'))->delete();
      DB::table('addresses')->where('id',  $request->input('id'))->delete();

         return response()->json(array('message' =>'Addresse de livraison supprimer du compte', 'success'=>true, 201 ));

   }
}
