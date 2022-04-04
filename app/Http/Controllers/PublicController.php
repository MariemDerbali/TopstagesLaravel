<?php

namespace App\Http\Controllers;

use App\Models\OffreStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function GetDepartements()
    {
        //Obtenir la liste de tous les départements activés
        $deps =  DB::collection('departments')->where('etat', 'active')->get();
        return response()->json([
            'status' => 200,
            'deps' => $deps
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
}
