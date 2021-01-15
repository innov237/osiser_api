<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// groupe de route pour l'authentification
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'Auth\AuthController@login');
    Route::post('register', 'Auth\AuthController@register');
    Route::get('creerTypeUser', 'Auth\AuthController@creerTypeUser');
    Route::get('verifiparametre', 'Auth\AuthController@verifiparametre');
    Route::group([
    //   'middleware' => 'auth:api'
    ], function() {
        Route::get('filtrelisteclient', 'Auth\AuthController@filtrelisteclient');
        Route::get('logout', 'Auth\AuthController@logout');
        Route::get('user', 'Auth\AuthController@user');
        Route::get('menbre', 'Auth\AuthController@menbre');
        Route::get('client', 'Auth\AuthController@client');
        Route::get('livreur', 'Auth\AuthController@livreur');
        Route::post('telechargerImage', 'Auth\AuthController@telechargerImage');
        Route::post('modifierPassword', 'Auth\AuthController@modifPassword');
    });
});
// groupe de route pour le stock
Route::group([
    'prefix' =>'stock'
], function () {
    //categorie
    Route::get('listeCategorie','CategorieController@listeCategorie');
    Route::get('categorieGroupee','CategorieController@categorieGroupee');
    Route::get('listeSousCategorie','CategorieController@listeSousCategorie');
    Route::get('listeCategorieParent', 'CategorieController@listeCategorieParent');
    // Produit
    Route::get('listeProduit', 'ProduitController@listeproduit');
    Route::get('rechercherProduit', 'ProduitController@rechercherProduit');
    Route::get('produitParCategorie', 'ProduitController@produitParCategories');
    // commentaire produit
    Route::get('afficherCommentaireProduit', 'CommentaireController@afficheCommentaireProduit');
    Route::get('afficheCommentaireCommentaire', 'CommentaireController@afficheCommentaireCommentaire');
    Route::get('generate-slug', 'ProduitController@sulgProduit');
    Route::get('produitBySlug', 'ProduitController@produitBySlug');
    Route::get('getAllPromo', 'ProduitController@getAllpromotion');

    Route::group([
    //   'middleware' => 'auth:api'
    ], function() {
        // crud categorie
        Route::post('ajouterCategorie','CategorieController@ajouterCategorie');
        Route::post('modifierCategorie','CategorieController@modifierCategorie');
        Route::post('supprimeCategorie','CategorieController@supprimeCategorie');
        Route::post('telechargerImageCategorie', 'CategorieController@telechargerImageCategorie');
        // alert et rupture produit
        Route::get('alertProduit','CategorieController@alertProduit');
        Route::get('ruptureProduit','CategorieController@ruptureProduit');
        // gestion des ventes
        Route::post('EnregistrerVente', 'VentesController@EnregistrerVente');
        Route::post('annulerVente', 'VentesController@annulerVente');
        Route::get('afficherVente', 'VentesController@afficherVente');
        // crud produit
        Route::post('enregistrerProduit', 'ProduitController@enregistrerProduit');
        Route::post('approvisionnerProduit', 'ProduitController@approvisionnerProduit');
        Route::post('telechargerImage', 'ProduitController@telechargerImage');
        Route::post('modifierProduit', 'ProduitController@modifierProduit');
        Route::post('SupprimerProduit', 'ProduitController@SupprimerProduit');
        Route::get('listeProduitSupprimer', 'ProduitController@listeProduitSupprimer');
        Route::post('enregistrerProduit', 'ProduitController@enregistrerProduit');
        Route::post('ajouterEnPromotion', 'ProduitController@ajouterEnPromotion');
        Route::post('retirerEnPromotion', 'ProduitController@retirerEnPromotion');
        //commentaire
        Route::post('enregistrerCommentaire', 'CommentaireController@enregistrerCommentaire');
        Route::post('supprimerCommentaire', 'CommentaireController@supprimerCommentaire');
    });
});

// groupe de route commande et livraison
Route::group([
    'prefix' => 'commande'
], function () {

    //créer etat commande
    Route::get('CreerEtatCommande', 'EtatCommandeController@CreerEtatCommande');

    //créer etat livraison
    Route::get('CreerEtatLivraison', 'EtatLivraisonController@CreerEtatLivraison');

    Route::group([
            //   'middleware' => 'auth:api'
              ],
        function () {
         //address de livraison

        Route::post('changeDefaultCoordonnee', 'AddressController@changeDefaultAdresse');
        Route::post('modifierCoordonnee', 'AddressController@modifierAdresse');
        Route::post('supprimerCoordonnee', 'AddressController@supprimerAdresse');
        Route::post('coordonnee', 'AddressController@ajouterCoordonnee');
        Route::post('SupprimeDefaulCoordonne', 'AddressController@supprimeDefaultAdresse');
        Route::get('listeadress', 'AddressController@listeAdresseClient');

        //point_livraison
        Route::post('creerpointlivraison', 'PointLivraisonController@creerpointlivraison');
        Route::post('listepointlivraison', 'PointLivraisonController@listepointlivraison');
        Route::get('listeCommandeClient', 'CommandeController@listeCommandeClient');

        // livraison
        Route::get('livraison_commande', 'LivraisonController@livraison_commande');
        Route::get('listeLivraison', 'LivraisonController@listeLivraison');
        Route::get('listeLivraisonParEtat', 'LivraisonController@listeLivraisonParEtat');
        Route::post('creerLivraison', 'LivraisonController@creerLivraison');
        Route::post('annulerLivraison', 'LivraisonController@annulerLivraison');
        Route::post('ChangeEtatlivraison', 'LivraisonController@ChangeEtatlivraison');
        Route::post('listelivraisonjourTotal', 'LivraisonController@listelivraisonjourTotal');
        Route::post('listelivraisonjourParEtat', 'LivraisonController@listelivraisonjourParEtat');
        Route::post('listeLivraisonSemaine', 'LivraisonController@listeLivraisonSemaine');
        Route::post('listeLivraisonSemaineParEtat', 'LivraisonController@listeLivraisonSemaineParEtat');
        Route::post('livraison_mois', 'LivraisonController@livraison_mois');
        Route::post('livraison_moisParEtat', 'LivraisonController@livraison_moisParEtat');

        //etat_livraison
        Route::get('getEtatlivraison', 'EtatLivraisonController@getEtatlivraison');

        //etat_commande
        Route::get('getEtatcommande', 'EtatCommandeController@getEtatCommande');

        // livreur
        Route::post('creerLivreur', 'LivreurController@creerLivreur');
        Route::post('modifierLivreur', 'LivreurController@modifierLivreur');
        Route::get('listeLivreur', 'LivreurController@listeLivreur');
        Route::post('supprimerLivreur', 'LivreurController@supprimerLivreur');

        //commande
        Route::post('listeCommandejourParEtat', 'CommandeController@listeCommandejourParEtat');
        Route::post('listeCommandejourTotal', 'CommandeController@listeCommandejourTotal');
        Route::post('listecommandeSemaineParEtat', 'CommandeController@listecommandeSemaineParEtat');
        Route::post('listecommandeSemaineTous', 'CommandeController@listecommandeSemaineTous');
        Route::post('listecommande_moisParEtat', 'CommandeController@listecommande_moisParEtat');
        Route::post('listecommande_moisTotal', 'CommandeController@listecommande_moisTotal');
        Route::post('listeCommandeParEtat', 'CommandeController@listeCommandeParEtat');
        Route::get('listeCommande', 'CommandeController@listeCommande');
        Route::post('ChangeEtatCommande', 'CommandeController@ChangeEtatCommande');
        Route::post('listeCommandeClient', 'CommandeController@listeCommandeClient');
        Route::post('verifieCommande', 'CommandeController@verifieCommande');
        Route::post('annulationCommande', 'CommandeController@annulationCommande');
        Route::get('listeproduitCommande', 'CommandeController@listeproduitCommande');
        Route::post('commande', 'CommandeController@commandeClient');


        //client
        Route::get('filtrelisteclient', 'ClientController@filtrelisteclient');
    });
});

// route pour les parametres

Route::group([
    'prefix' => 'setting'
], function () {
    Route::get('listDevise', 'DeviseController@listDevise');
    Route::get('listPays', 'PaysController@listePays');
    Route::post('ajouterType', 'TypeUserController@ajouterType');
    Route::get('listeType', 'TypeUserController@listeType');
    Route::group([
        // 'middleware' => 'auth:api'
    ], function () {
        Route::post('telechargerImage','PaysController@telechargerImage');
        Route::get('DeviseDefault', 'DeviseController@DeviseDefault');
        Route::post('ajouterDevise', 'DeviseController@ajouterDevise');
        Route::post('ajouterPays', 'DeviseController@ajouterPays');
    });
});

Route::group([
    'prefix' => 'forum'
], function () {
    
    Route::get('liste-categorie', 'CategorieSujetController@listCategorie');
    Route::get('liste-sujet-categorie', 'SujetController@listSujetCategorie');
    Route::group([
        // 'middleware' => 'auth:api'
    ], function () {
        Route::get('type-abonnement', 'TypeAbonnerController@listType');
        Route::get('list-discussion-sujet', 'DiscussionController@listDiscussionSujet');
        Route::post('creer-categorie','CategorieSujetController@createCategorie');
        Route::post('supprimer-categorie','CategorieSujetController@supprimerCategorie');
        Route::post('creer-sujet','SujetController@creerSujet');
        Route::post('supprimer-sujet','SujetController@deleteSujet');
        Route::post('abonnement-sujet','AbonnerController@abonnement');
        Route::get('demande-abonnement','AbonnerController@demandeAbonnement');
        Route::post('creer-discussion','DiscussionController@creeDiscussion');
        Route::post('supprimer-discussion', 'DiscussionController@supprimerDiscussion');
        Route::post('delete-abonner','AbonnerController@deleteAbonner');
        Route::post('deactivate-abonner','AbonnerController@deactivateAbonner');
        Route::post('validation-abonnement','AbonnerController@valideAbonnementExpert');
        Route::post('regeter-abonnement','AbonnerController@rejetAbonnement');
    });
});

Route::group([
    'prefix' => 'video'
], function () {
    
    Route::get('liste-video', 'VideoController@listVideo');
   
    Route::group([
        // 'middleware' => 'auth:api'
    ], function () {
        Route::post('creer-video','VideoController@ajouterVideos');
        Route::post('supprimer-video','VideoController@supprimerVideo');
    });
});