<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class categorie extends Model
{
    public static function groupeCategorie(){
        $records = self::select('categories.*')->where('est_actif', 1)->orderBy('created_at','ASC')->get();
        $categorie = [];
        $categorie_id = [];
        $souscategorie = [];
        foreach ($records as $record) {
            if($record->id_categorie_parent!=null && $record != null){
                 $categorie_id[$record->id_categorie_parent]->attributes['categorie_fils'][] = $record;
                $sousCategorie = self::where('id_categorie_parent',$record->id)->orderBy('created_at','ASC')->get();
                $record->attributes['categorie_fils'] = $sousCategorie;
            }else {
                $record->attributes['categorie_fils'] = [];
                $categorie_id[$record->id] = $record;
                $categorie[] = $record;
            }
        }
        return $categorie;
    }
    
    public static function groupeCategorie2(){
        $records = self::select('categories.*')->where('est_actif', 1)->orderBy('created_at','ASC')->get();
        $categorie = [];
        $categorie_id = [];
        $souscategorie = [];
        foreach ($records as $record) {
            if($record->id_categorie_parent){
                $categorie_id[$record->id_categorie_parent]->attributes['categorie_fils'][] = $record;
                $sousCategorie = self::where('id_categorie_parent',$record->id)->orderBy('created_at','ASC')->get();
                $record->attributes['categorie_fils'] = $sousCategorie;
            }else {
                $record->attributes['categorie_fils'] = [];
                $categorie_id[$record->id] = $record;
                $categorie[] = $record;
            }
        }
        return $categorie;
    }
}
