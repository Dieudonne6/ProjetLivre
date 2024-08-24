<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Livre;
use App\Models\Panier;
use App\Models\Commande;
use App\Models\Categorie;

class AuthController extends Controller
{

    // INSCRIPTION



/**
     * Register user
     * @OA\Post(
     *      path="/api/register",
     *      tags={"Register"},
     *      @OA\RequestBody(
     *           required=true,
     *              @OA\MediaType(
     *                  mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      format="email"
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="string",
     *                      format="binary"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="telephone",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="statut",
     *                      type="integer"
     *                  ),
     *              ),
     *              example={
     *                  "name":"jean",
     *                  "email":"jean@gmail.com",
     *                  "image":"image-file.jpg",
     *                  "password":"password",
     *                  "telephone":"12345678",
     *                  "statut":1
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string"),
     *              @OA\Property(property="type", type="string"),
     *              @OA\Property(property="msg", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="object"),
     *          )
     *      )
     * )
     */
    public function register(Request $request) {
        $validations = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users|max:155',
            // 'image' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|min:6',
            'telephone' => 'required|max:8',
            'statut' => 'required'
        ]);


        if ($validations->fails()) {
            $errors = $validations->errors();

            return response()->json([
                'errors' => $errors,
                'status' => 401
            ]);
        }

        if ($validations->passes()) {
            // $imageName = "kokoko";
            $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
            // if(($request->statut) === 1){

            //     // l'utilisateur est Vendeur

            //     $user = User::create([
            //         'name' => $request->name,
            //         'email' => $request->email,
            //         'image' => $imageName,
            //         'password' => Hash::make($request->password),
            //         'telephone' => $request->telephone,
            //         'statut' => 1,
            //     ]);
    
            //     Storage::disk('public')->put($imageName, file_get_contents($request->image));
            //     $token = $user->createToken('auth_token')->plainTextToken;
            //     return response()-> json([
            //         'token' => $token,
            //         'type' => 'Bearer',
            //         'msg' => 'inscription effectuer avec succes',
            //         'statut' => 200
            //     ]);
            // }

            // l'utilisateur est un Vendeur statut = 0
            // l'utilisateur est un Acheteur statut = 1

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'image' => $imageName,
                'password' => Hash::make($request->password),
                'telephone' => $request->telephone,
                'statut' => $request->statut,
            ]);

            Storage::disk('public')->put($imageName, file_get_contents($request->image));
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()-> json([
                'token' => $token,
                'type' => 'Bearer',
                'msg' => 'inscription effectuer avec succes',
                'status' => 200
            ]);
           
        }
    }


    // CONNEXION

/**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Login"},
 *     summary="Se connecter et obtenir un token d'authentification",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"email", "password"},
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email",
 *                     example="user@example.com",
 *                     description="L'adresse e-mail de l'utilisateur"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string",
 *                     format="password",
 *                     example="password123",
 *                     description="Le mot de passe de l'utilisateur"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Connexion réussie et token généré",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="token",
 *                 type="string",
 *                 example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
 *                 description="Le token d'authentification JWT"
 *             ),
 *             @OA\Property(
 *                 property="type",
 *                 type="string",
 *                 example="Bearer",
 *                 description="Le type du token d'authentification"
 *             ),
 *             @OA\Property(
 *                 property="status",
 *                 type="integer",
 *                 example=200,
 *                 description="Code de statut HTTP"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Échec de la connexion, e-mail ou mot de passe incorrect",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="msg",
 *                 type="string",
 *                 example="email ou mot de passe incorrect",
 *                 description="Message d'erreur"
 *             ),
 *             @OA\Property(
 *                 property="status",
 *                 type="integer",
 *                 example=401,
 *                 description="Code de statut HTTP"
 *             )
 *         )
 *     )
 * )
 */
    public function login(Request $request) {
        if(!Auth::attempt($request->only('email', 'password'))) {
            return response() -> json([
                'msg' => 'email ou mot de passe incorrect',
                'status' => 401
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()-> json([
            'token' => $token,
            'type' => 'Bearer',
            'status' => 200
        ])->cookie('jwt', $token);

    }



    //RECUPERATION DONNEE DE L'UTILISATEUR CONNECTER
 /**
     * Get user data
     * @OA\Get(
     *      path="/api/user",
     *      tags={"User"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="user", type="object"),
     *              @OA\Property(property="userlivre", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="msg", type="string"),
     *              @OA\Property(property="status", type="integer"),
     *          )
     *      ),
     * 
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     )
     * )
     */
    public function user(Request $request) {
        $usercon = $request->user();
        $idusercon = $usercon->id;
        $statutuser = $usercon->statut;

        // dd($iduser);

        if ($statutuser === 0) {

            $userlivre = Livre::where('id_vendeur','=', $idusercon)->get();
            // dd($userlivre);
    
            return response() -> json([
                'user' => $usercon,
                'userlivre' => $userlivre,
                'msg' => 'livre de l\'utilisateur recuperer avec succes',
                'status' => 200
            ]);

        }

        $alllivre = Livre::all();
        // dd($userlivre);

        return response() -> json([
            'user' => $usercon,
            'alllivre' => $alllivre,
            'msg' => 'livre de l\'utilisateur recuperer avec succes',
            'status' => 200
        ]);

    }

    // AJOUT D'UN LIVRE
/**
 * Create a new book
 * @OA\Post(
 *      path="/api/createlivre",
 *      tags={"Livre"},
 *      security={{"bearerAuth":{}}},
 *      @OA\RequestBody(
 *           required=true,
 *           @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(property="nomL", type="string"),
 *                  @OA\Property(property="categorieL", type="string"),
 *                  @OA\Property(property="description", type="string"),
 *                  @OA\Property(property="path", type="string", format="binary"),
 *                  @OA\Property(property="statutL", type="integer"),
 *                  @OA\Property(property="date", type="string", format="date"),
 *                  @OA\Property(property="prixL", type="number")
 *              ),
 *              example={
 *                  "nomL":"Titre du Livre",
 *                  "categorieL":"Categorie",
 *                  "description":"Description du livre",
 *                  "path":"file.pdf",
 *                  "statutL":1,
 *                  "date":"2024-07-24",
 *                  "prixL":100
 *              }
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Livre ajouté avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="msg", type="string"),
 *              @OA\Property(property="status", type="integer"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Validation error",
 *          @OA\JsonContent(
 *              @OA\Property(property="errors", type="object"),
 *              @OA\Property(property="status", type="integer"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *           description="Unauthorized",
 *           @OA\JsonContent(
 *               @OA\Property(property="msg", type="string"),
 *               @OA\Property(property="status", type="integer")
 *          )
 *      )
 * )
 */
    public function createlivre(Request $request) {
        $user = $request->user();
        $iduser = $user->id;

        $validations = Validator::make($request->all(), [
            'nomL' => 'required|string',
            'categorieL' => 'required|string',
            'description' => 'required|max:300',
            // 'path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'path' => 'required|mimes:pdf|max:10000',  // Validation pour le fichier PDF
            'statutL' => 'required',
            'date' => 'required',
            'prixL' => 'required'
            // 'id_vendeur' => 'required'
        ]);


        if ($validations->fails()) {
            $errors = $validations->errors();

            return response()->json([
                'errors' => $errors,
                'status' => 400
            ]);
        }

        if ($validations->passes()) {
            // $imageName = "kokoko";
            // $imageNameLivre = Str::random(32).".".$request->path->getClientOriginalExtension();
          

                    // Lire le contenu du fichier PDF
                    // $pdfContent = file_get_contents($request->path);
                    $pdfContent = file_get_contents($request->file('path')->getRealPath());
                    $encodedPdfContent = base64_encode($pdfContent);
            // livre GRATUIT statutL == 0 et prixl == 0
            // livre PREMIUM statutL == 1 et prixL == $request->prixL

            $user = Livre::create([

                'nomL' => $request->nomL,
                'categorieL' => $request->categorieL,
                'description' => $request->description,
                'path' =>  $encodedPdfContent,
                'date' => $request->date,
                'prixL' => $request->prixL,
                'statutL' => $request->statutL,
                'id_vendeur' => $iduser,
            ]);

            // Storage::disk('public')->put($imageNameLivre, file_get_contents($request->path));
            return response()-> json([
                'msg' => 'livre ajouter avec succes',  
                'status' => 200
            ]);
        
        }

    }

    // DECONNEXION
/**
     * Logout user
     * @OA\Post(
     *      path="/api/logout",
     *      tags={"Logout"},
     *           security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Utilisateur déconnecté",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string"),
     *              @OA\Property(property="status", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent(
     *               @OA\Property(property="msg", type="string"),
     *               @OA\Property(property="status", type="integer")
     *          )
     *      )
     * )
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'msg' => 'Utilisateur deconnecte',
            'status' => 200
        ]);
    }
    
    // Ajout de livre au panier
/**
     * Add book to cart
     * @OA\Post(
     *      path="/api/addcart/{id}",
     *      tags={"Panier"},
     *          security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Livre ajouté au panier avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="panier", type="object"),
     *              @OA\Property(property="status", type="integer"),
     *              @OA\Property(property="msg", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent(
     *               @OA\Property(property="msg", type="string"),
     *               @OA\Property(property="status", type="integer")
     *          )
     *      )
     * )
     */
    public function addart(Request $request, $id){
        $userconnect = $request->user();

        $idlivre = $id;
        $infolivre = Livre::find($idlivre);

        $cart = new Panier();
        $cart->nom = $infolivre->nomL;
        $cart->url = $infolivre->path;
        $cart->prix = $infolivre->prixL;
        $cart->idlivre = $infolivre->id;
        $cart->idvendeur = $infolivre->id_vendeur;
        $cart->idacheteur = $userconnect->id;

        $cart->save();

        return response() ->json ([
            'panier' => $cart,
            'status' => 200,
            'msg' => 'livre ajouter au panier avec succes'
        ]);
    }

    // voir les livres du panier
 /**
     * View cart
     * @OA\Get(
     *      path="/api/cart",
     *      tags={"Panier"},
     *          security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Affichage des livres ajoutés au panier",
     *          @OA\JsonContent(
     *              @OA\Property(property="panier", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="prixtotal", type="number"),
     *              @OA\Property(property="status", type="integer"),
     *              @OA\Property(property="msg", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent(
     *               @OA\Property(property="msg", type="string"),
     *               @OA\Property(property="status", type="integer")
     *          )
     *      )
     * )
     */
    public function viewcart(Request $request) {
        $acheteur = $request->user();
        $idacheteur = $acheteur->id;

        $livresPanier = Panier::where('idacheteur', $idacheteur)->get();
        $prixtotal = Panier::where('idacheteur', $idacheteur)->sum('prix');

        return response()->json([
            'livresPanier' => $livresPanier,
            'prixtotal' => $prixtotal,
            'status' => 200, 
            'msg' => 'affichage des livres ajouter au panier'
        ]);    
    }

    // supprimer un livre du panier
 /**
     * Remove book from cart
     * @OA\Delete(
     *      path="/api/deletelivrecart/{id}",
     *      tags={"Panier"},
     *          security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Livre retiré du panier avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string"),
     *              @OA\Property(property="status", type="integer"),
     *          )
     *      ),
     *      @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *           @OA\JsonContent(
     *               @OA\Property(property="msg", type="string"),
     *               @OA\Property(property="status", type="integer")
     *          )
     *      )
     * )
     */
    public function deletelivrecart ($id) {
        $livrepanier = Panier::find($id);
        $livrepanier->delete();

        return response()->json([
            'status' => 200,
            'msg' => 'livre supprimer du panier avec succes',
        ]);
    }

    // formulaire contenant les information pour effectuer le paiement
/**
 * @OA\Get(
 *      path="/api/paiement",
 *      tags={"Paiement"},
 *          security={{"bearerAuth":{}}},
 *          summary="Obtenir les informations nécessaires pour effectuer le paiement",
 *      @OA\Response(
 *          response=200,
 *          description="Informations de paiement récupérées avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="utilisateur", type="object"),
 *              @OA\Property(property="prixtotal", type="number"),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *           description="Unauthorized",
 *           @OA\JsonContent(
 *               @OA\Property(property="msg", type="string"),
 *               @OA\Property(property="status", type="integer")
 *          )
 *      )
 * )
 */
    public function paiement(Request $request) {
        $utilisateur = $request->user();
        $idUtilisateur = $utilisateur->id;

        $prixtotal = Panier::where('idacheteur', $idUtilisateur)->sum('prix');

        return response()->json([
            'utilisateur' => $utilisateur,
            'prixtotal' => $prixtotal,
            'status' => 200,
            'msg' => 'Donnees necessaire au paiement recuperees avec succes'
        ]);

    }

    // valider le paiememt
/**
 * @OA\Post(
 *      path="/api/validatepaiement",
 *          tags={"Paiement"},
 *          security={{"bearerAuth":{}}},
 *          summary="Valider le paiement et enregistrer la commande",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  type="object",
 *                  required={"solde"},
 *                  @OA\Property(property="solde", type="number", format="float", example=150, description="Le solde disponible pour effectuer le paiement")
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Paiement validé et commande enregistrée avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="integer", example=200),
 *              @OA\Property(property="msg", type="string", example="Achat effectué avec succès")
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Erreur lors du paiement, solde insuffisant",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="integer", example=400),
 *              @OA\Property(property="msg", type="string", example="Solde insuffisant pour effectuer l'achat")
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *           description="Unauthorized",
 *           @OA\JsonContent(
 *               @OA\Property(property="msg", type="string"),
 *               @OA\Property(property="status", type="integer")
 *          )
 *      )
 * )
 */
    public function validatepaiement (Request $request) {
        $infoachet = $request->user();
        $idacheteur = $request->user()->id;
        $prixtotal = Panier::where('idacheteur', $idacheteur)->sum('prix');

        if(($prixtotal) <= ($infoachet->solde)) {

            
            // recuperer tout les id des livres du panier
            $idlivres = Panier::where('idacheteur', $idacheteur)->pluck('idlivre');
            $idlivresnomlivre = Panier::where('idacheteur', $idacheteur)->select('idlivre', 'nom')->get();

        // Créer un tableau associatif avec ID du livre comme clé et nom du livre comme valeur
        $livresArray = [];
        foreach ($idlivresnomlivre as $idlivrenomlivre) {
            $livresArray[$idlivrenomlivre->idlivre] = $idlivrenomlivre->nom;
        }

        // Convertir le tableau associatif en une chaîne de caractères
        $livresString = '';
        foreach ($livresArray as $id => $nom) {
            $livresString .= $id . ':' . $nom . ',' . ' '; // ajoute la nouvelle chaîne à la fin de $livresString. Cela signifie que la chaîne accumulée devient plus longue à chaque itération de la boucle.
        }

        // Retirer la dernière virgule
        $livresString = rtrim($livresString, ',');

        foreach($idlivres as $idlivre){

            $infoLivreSelect = Livre::find($idlivre);
        
            $idvend = $infoLivreSelect->id_vendeur;
            if($idvend){

                $infovendeur = User::find($idvend);
                $infoacheteur = User::find($idacheteur);

                $infovendeur->solde = $infovendeur->solde + $infoLivreSelect->prixL;
                $infovendeur->save();

                $infoacheteur->solde = $infoacheteur->solde - $infoLivreSelect->prixL;
                $infoacheteur->save();

            }
        };



        // Enregistrer la commande

        $commande = new Commande;
        $commande->idacheteur = $idacheteur;
        $commande->livre = $livresString;
        $commande->prixtotal = $prixtotal;
        $commande->save();

        // vider le panier une fois le paiement terminer

        $infopanierachets = Panier::where('idacheteur', $idacheteur)->get();
        foreach ($infopanierachets as $infopanierachet) {
            $infopanierachet->delete();
        }
        
        return response()->json([
            'status' => 200,
            'msg' => "achat effectuer avec succes"
        ]);

        } else {
            return response()->json([
                'status' => 400,
                'msg' => "solde insuffisant pour effetuer l'achat"
            ]);
        }

    }

    // LISTE DE TOUS LES LIVRES
/**
 * @OA\Get(
 *      path="/api/listelivre",
 *      tags={"Livre"},
 *      summary="Obtenir la liste de tous les livres",
 *      @OA\Response(
 *          response=200,
 *          description="Liste des livres retournée avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="livres", type="array", @OA\Items(type="object")),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      )
 * )
 */
    public function listelivre() {

        $livres = Livre::with('categorie')->get();
        // $categories = Categorie::all();
        
        // dd($livres);
        
        return response()->json([
            'livres' => $livres,
            'status' => 200,
            'msg' => "liste des livres retournee avec succes"
        ]);
    }

    // RECUPERER LA LISTE DES CATEGORIES
/**
 * @OA\Get(
 *      path="/api/categories",
 *      tags={"Categories"},
 *      summary="Obtenir la liste des catégories",
 *      @OA\Response(
 *          response=200,
 *          description="Liste des catégories retournée avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="categories", type="array", @OA\Items(type="object")),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      )
 * )
 */
    public function categorie(){
        $categories = Categorie::all();

        return response()->json([
            // 'idcategorie' => $categories->id,
            'categories' => $categories,
            'status' => 200,
            'msg' => "liste des categories retournee avec succes"
        ]);
    }

    // LISTE DES LIVRES PAR CATEGORIE
/**
 * @OA\Get(
 *      path="/api/livrecategorie/{categorie}",
 *      tags={"Livre"},
 *      security={{"bearerAuth":{}}},
 *      summary="Obtenir la liste des livres par catégorie",
 *      @OA\Parameter(
 *          name="categorie",
 *          in="path",
 *          required=true,
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Livres triés par catégorie retournés avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="livreCat", type="array", @OA\Items(type="object")),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      ),
 *      @OA\Response(
*            response=401,
 *           description="Unauthorized",
 *           @OA\JsonContent(
 *               @OA\Property(property="msg", type="string"),
 *               @OA\Property(property="status", type="integer")
 *          )
 *      )
 * )
 */
    public function livrecategorie ($categorie) {

        // $category = $categorie;
        $livreCat = Livre::where('categorieL', '=', $categorie)->get();

        return response()->json([
            'livreCat' => $livreCat,
            'status' => 200,
            'msg' => "livre trié par categories avec succes"
        ]);
    }

    // RECHARGER SON SOLDE
/**
 * @OA\Put(
 *      path="/api/rechargesolde",
 *      tags={"User"},
 *      security={{"bearerAuth":{}}},
 *      summary="Recharger le solde de l'utilisateur",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(property="solde", type="number")
 *              ),
 *              example={
 *                  "solde": 100
 *              }
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Solde rechargé avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="utilisateurConnect", type="object"),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Erreur de validation",
 *          @OA\JsonContent(
 *              @OA\Property(property="errors", type="object"),
 *              @OA\Property(property="status", type="integer")
 *          )
 *      ),
 *      @OA\Response(
*            response=401,
 *           description="Unauthorized",
 *           @OA\JsonContent(
 *               @OA\Property(property="msg", type="string"),
 *               @OA\Property(property="status", type="integer")
 *          )
 *      )
 * )
 */
    public function rechargesolde (Request $request) {

        $validation = Validator::make($request->all(), [
            'solde' => 'required|numeric|min:0',
        ]);

        
        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                'errors' => $errors,
                'status' => 400
            ]);
        }


        if ($validation->passes()) {

            $utilisateurConnect = $request->user();
            // $idUtilisateurConnect = $request->user()->id;
    
            // $modifSolde = User::find($idUtilisateurConnect);
            $utilisateurConnect->solde = $request->solde;
            $utilisateurConnect->save();
    
            return response()->json ([
                'status' => 200,
                'utilisateurConnect' => $utilisateurConnect,
                'msg' => "Solde rechargé avec succès"
            ]);
    
        }

       

    }

    // MOT DE PASSE OUBLIE
/**
 * @OA\Put(
 *      path="/api/modifpassword",
 *      tags={"User"},
 *      summary="Réinitialiser le mot de passe de l'utilisateur",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  @OA\Property(property="email", type="string", format="email"),
 *                  @OA\Property(property="password", type="string")
 *              ),
 *              example={
 *                  "email": "jean@gmail.com",
 *                  "password": "newpassword"
 *              }
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Mot de passe réinitialisé avec succès",
 *          @OA\JsonContent(
 *              @OA\Property(property="infoUser", type="object"),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Erreur de validation ou email non retrouvé",
 *          @OA\JsonContent(
 *              @OA\Property(property="errors", type="object"),
 *              @OA\Property(property="status", type="integer"),
 *              @OA\Property(property="msg", type="string")
 *          )
 *      )
 * )
 */
    public function modifpassword(Request $request){

        $validation = Validator::make($request->all(), [
            'email' => 'required|email|max:155',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                'errors' => $errors,
                'status' => 401
            ]);
        }

        if ($validation->passes()) {
            $emailUser = $request->email;

            $infoUser = User::where('email', $emailUser)->first();
    
            if($infoUser) {
                $infoUser->password = Hash::make($request->password);
                $infoUser->save();

                return response()->json([
                    'status' => 200,
                    'infoUser' => $infoUser,
                    'msg' => "mot de passe reinitialisé avec success"
                ]);
            }
            else {
                return response()->json([
                    'status' => 401,
                    'msg' => "email non retrouvé"
                ]);
            }
    
           
        }
        
        
    }


    // 8|dodaiF8ycBNoy0yv4NlAtavn17k52Gh7UWN4wVcK1238b1ca

    // 10|sSooLCXValpIhZZfECCPbI8jJV9MWTI1Mz6Q7y7oe8a34254
}
