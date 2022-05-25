<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stagiaire;
use App\Models\OffreStage;
use App\Models\DemandeStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicController extends Controller
{
    public function GetServices()
    {
        //Obtenir la liste de tous les services activés
        $services =  DB::collection('services')->where('etat', 'active')->get();
        return response()->json([
            'status' => 200,
            'services' => $services
        ]);
    }

    public function getOffres()
    {

        //obtenir la liste de toutes les offres de stage pour le stagiaire
        $offres = OffreStage::where([['etatoffre', '!=', 'inactive'], ['etatpartage', '!=', 'unpublished']])->get();

        return response()->json([
            'status' => 200,
            'offres' => $offres,

        ]);
    }

    public function postOffreDemandee(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'domaine' => 'required',
            'stagiaireID' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
                'message' => 'Veuillez choisir le domaine et le type de stage'
            ]);
        } else {
            /*  $check = $this->checkDemandeTimeInterval($request->stagiaireID);
            if ($check) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Vous avez déjà fait une demande de stage. Merci de patienter pour faire une prochaine demande'
                ]);
            } else {*/
            $offredemande = new DemandeStage;
            if ($request->has('sujet') && $request->has('encadrant')) {
                $offredemande->sujet = $request->sujet;
                $offredemande->encadrant = $request->encadrant;
            }
            $offredemande->domaine = $request->input('domaine');
            $offredemande->type = $request->input('type');


            $idstagiaire = $request->stagiaireID;
            $stagiaire = Stagiaire::find($idstagiaire);

            $stagiairearray[] = [
                'stagiaireId' => $stagiaire->id,
                'nom' => $stagiaire->nom,
                'prenom' => $stagiaire->prenom,
                'email' => $stagiaire->email,
            ];

            $offredemande->stagiaire =  $stagiairearray;



            $offredemande->save();

            return response()->json([
                'status' => 200,
                'offredemande' => $offredemande
            ]);
            //}
        }
    }

    public function checkDemandeTimeInterval($id)
    {

        return DB::collection('demande_stages')
            ->where('stagiaire.stagiaireId', $id)->where('created_at', now())->exists();
    }
}
