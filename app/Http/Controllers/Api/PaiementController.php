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
 * Get payment information
 * @OA\Get(
 *      path="/api/paiement",
 *      tags={"Payment"},
 *      security={{"bearerAuth":{}}},
 *      summary="Retrieve payment details including total cart price",
 *      @OA\Response(
 *          response=200,
 *          description="Payment information retrieved successfully",
 *          @OA\JsonContent(
 *              @OA\Property(property="user", type="object"),
 *              @OA\Property(property="amount", type="number", example=75.50),
 *              @OA\Property(property="msg", type="string", example="Payment information retrieved successfully")
 *          )
 *      )
 * )
 */
    public function paiement(Request $request) {
        $utilisateur = $request->user();
        $idUtilisateur = $utilisateur->id;

        $prixtotal = Panier::where('idacheteur', $idUtilisateur)->sum('prix');

        return response()->json([
            'user' => $utilisateur,
            'amount' => $prixtotal,
            // 'status' => 200,
            'msg' => 'Payment information retrieved successfully'
        ], 200);

    }

    // valider le paiememt
/**
 * Validate payment
 * @OA\Post(
 *      path="/api/validatepaiement",
 *      tags={"Payment"},
 *      security={{"bearerAuth":{}}},
 *      summary="Validate payment and create an order",
 *      @OA\Response(
 *          response=200,
 *          description="Payment completed successfully",
 *          @OA\JsonContent(
 *              @OA\Property(property="msg", type="string", example="Purchase completed successfully")
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Insufficient balance",
 *          @OA\JsonContent(
 *              @OA\Property(property="msg", type="string", example="Insufficient balance to complete the purchase")
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="No item in cart",
 *          @OA\JsonContent(
 *              @OA\Property(property="msg", type="string", example="Error, No data in your cart")
 *          )
 *      )
 * )
 */
    public function validatepaiement (Request $request) {
        $infoachet = $request->user();
        $idacheteur = $request->user()->id;
        $prixtotal = Panier::where('idacheteur', $idacheteur)->sum('prix');


        if (!$prixtotal ) {
            return response()->json([
                'msg' => 'Error, No data in your cart'
            ], 404);
        }


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
                    'msg' => 'Purchase completed successfully'
                ], 200);

            } catch (\Exception $e) {

                DB::rollBack();

                return response()->json([
                    'msg' => 'Error'
                ], 500);
            }

        } else {
            return response()->json([
                // 'status' => 400,
                'msg' => "Insufficient balance to complete the purchase"
            ], 400);
        }

    }
}
