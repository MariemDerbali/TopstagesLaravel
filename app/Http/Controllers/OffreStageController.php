<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\OffreStage;

use App\Models\DemandeStage;
use Illuminate\Http\Request;
use App\Models\statistiqueOffres;
use Illuminate\Support\Facades\DB;
use App\Models\statistiqueStagiaire;
use Illuminate\Support\Facades\Validator;

class OffreStageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //obtenir la liste des offres de stage pour l'encadrant
        if (auth()->user()->role_id == 'Encadrant') {
            $EncadrantID = auth()->user()->_id;


            $offres = DB::collection('offre_stages')->where('encadrant.encadrantId', $EncadrantID)->get();
            return response()->json([
                'status' => 200,
                'offres' => $offres,


            ]);
        }
        //obtenir la liste des offres de stage pour le chef département
        else {
            $monservice = auth()->user()->service;


            $offres = DB::collection('offre_stages')->where('domaine', $monservice)->get();
            return response()->json([
                'status' => 200,
                'offres' => $offres,


            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //création des offres
        $validator = Validator::make($request->all(), [
            'sujet' => ['required', 'string', 'max:255'],
            'periode' => 'required|numeric',
            'technologies' => 'required',
            'type' => 'required',
            'domaine' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $offre = new OffreStage;
            $offre->sujet = $request->input('sujet');
            $offre->periode = $request->input('periode');
            $offre->technologies = $request->input('technologies');
            $offre->type = $request->input('type');

            $offre->domaine = $request->input('domaine');

            $offre->description = $request->input('description');

            $encadrantarray[] = [
                'encadrantId' => auth()->user()->_id,
                'nom' => auth()->user()->nom,
                'prenom' => auth()->user()->prenom,
                'email' => auth()->user()->email,
                'tel' => auth()->user()->tel,
            ];
            $offre->encadrant = $encadrantarray;
            $offre->etatoffre = 'active';
            $offre->etatpartage = 'unpublished';



            $offre->save();


            return response()->json([
                'status' => 200,
                'message' => 'Offre créée avec succès',
                'offre' => $offre
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return OffreStage::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Afficher le formulaire de modification de l'offre spécifié par id. 
        $offre = $this->show($id);
        if ($offre) {
            return response()->json([
                'status' => 200,
                'offre' => $offre,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucune offre trouvée',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Mettre à jour une offre
        $validator = Validator::make($request->all(), [
            'sujet' => ['required', 'string', 'max:255'],
            'periode' => 'required|numeric',
            'technologies' => 'required',
            'type' => 'required',
            'domaine' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $offre = OffreStage::find($id);

            if ($offre) {

                $offre->sujet = $request->input('sujet');
                $offre->periode = $request->input('periode');
                $offre->technologies = $request->input('technologies');
                $offre->type = $request->input('type');
                $offre->domaine = $request->input('domaine');
                $offre->description = $request->input('description');

                $offre->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Mise à jour effectuée avec succès',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Offre non trouvée',
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function desactiverOffre($id)
    {
        $offre  = OffreStage::find($id);
        if ($offre) {
            if ($offre->etatoffre == 'active') {
                $offre->etatoffre = 'inactive';
                $offre->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Offre est désactivée'
                ]);
            } else {
                $offre->etatoffre = 'active';
                $offre->save();
                return response()->json([
                    'status' => 201,
                    'message' => 'Offre est activée'
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Offre non trouvée"
            ]);
        }
    }

    public function publierOffre($id)
    {
        $offre  = OffreStage::find($id);
        if ($offre) {
            if ($offre->etatpartage == 'unpublished') {
                $offre->etatpartage = 'published';



                //save year to stat record if its a new year
                $currentYear = Carbon::now()->format('Y');
                $stat = statistiqueOffres::where(['annee' => $currentYear])->exists();

                if (!$stat) {
                    $statOffre = new statistiqueOffres;
                    $statOffre->annee =  $currentYear;
                    $statOffre->offres = 1;
                    $statOffre->save();
                }
                if ($stat) {

                    $statOffre = statistiqueOffres::where(['annee' => $currentYear])->first();
                    $statOffre->offres += 1;
                    $statOffre->save();
                }

                $offre->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Offre est publiée'
                ]);
            } else {
                $offre->etatpartage = 'unpublished';
                $offre->save();
                return response()->json([
                    'status' => 201,
                    'message' => 'La publication est annulée'
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Offre non trouvée"
            ]);
        }
    }

    public function Demandes()
    {
        $encadrant = auth()->user()->nom . ' ' . auth()->user()->prenom;

        $demandestraités = DemandeStage::where('etatdemande', 'Traitée')->where('encadrant', $encadrant)->get();

        if ($demandestraités) {
            return response()->json([
                'status' => 200,
                'demandes' => $demandestraités
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Demande non trouvé"
            ]);
        }
    }

    public function PriseEnCharge($id)
    {
        $demande  = DemandeStage::find($id);
        if ($demande) {
            if ($demande->etatprise !== 'vrai') {
                $demande->etatprise = 'vrai';





                //save year to stat record if its a new year
                $currentYear = Carbon::now()->format('Y');
                $stat = statistiqueStagiaire::where(['annee' => $currentYear])->exists();

                if (!$stat) {
                    $statStagiaire = new statistiqueStagiaire;
                    $statStagiaire->annee =  $currentYear;
                    $statStagiaire->stagiaires = 1;
                    $statStagiaire->save();
                }
                if ($stat) {

                    $statStagiaire = statistiqueStagiaire::where(['annee' => $currentYear])->first();
                    $statStagiaire->stagiaires += 1;
                    $statStagiaire->save();
                }




                $demande->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Stagiaire est pris en charge'
                ]);
            } else {
                $demande->etatprise = 'faux';
                $demande->save();
                return response()->json([
                    'status' => 201,
                    'message' => 'Prise en charge est annulée'
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Demande non trouvé"
            ]);
        }
    }



    public function statistiquesStagiaires()
    {
        $statStagiaires = statistiqueStagiaire::all();
        return response()->json([
            'status' => 200,
            'statStagiaires' => $statStagiaires,


        ]);
    }
}
