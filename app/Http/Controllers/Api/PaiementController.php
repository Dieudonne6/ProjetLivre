<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Livre;
use App\Models\Panier;
use App\Models\Commande;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaiementController extends Controller
{
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
            // 'status' => 200,
            'msg' => 'Donnees necessaire au paiement recuperees avec succes'
        ], 200);

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

            DB::beginTransaction();

            try {

                foreach($idlivres as $idlivre){

                    $infoLivreSelect = Livre::find($idlivre);

                    $vendeur = User::find($infoLivreSelect->id_vendeur);
                    $acheteur = User::find($idacheteur);

                    $vendeur->solde += $infoLivreSelect->prixL;
                    $vendeur->save();

                    $acheteur->solde -= $infoLivreSelect->prixL;
                    $acheteur->save();
                }

                $commande = Commande::create([
                    'idacheteur' => $idacheteur,
                    'livre' => $livresString,
                    'prixtotal' => $prixtotal
                ]);

                Panier::where('idacheteur', $idacheteur)->delete();

                DB::commit();

                return response()->json([
                    'msg' => 'Achat effectué avec succès'
                ], 200);

            } catch (\Exception $e) {

                DB::rollBack();

                return response()->json([
                    'msg' => 'Erreur lors du paiement'
                ], 500);
            }

        } else {
            return response()->json([
                // 'status' => 400,
                'msg' => "solde insuffisant pour effetuer l'achat"
            ], 400);
        }

    }
}
