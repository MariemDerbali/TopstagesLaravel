<?php

namespace App\Http\Controllers;

use App\Models\OffreStage;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
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
        //obtenir la liste des offres de stage pour l'encadrant et chef département
        $mondepartement = auth()->user()->departement;

        $offres = DB::collection('offre_stages')->where('domaine', $mondepartement)->get();
        return response()->json([
            'status' => 200,
            'offres' => $offres,
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
                $offre->etatoffre = $request->input('etatoffre');
                $offre->etatpartage = $request->input('etatpartage');
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
}
