<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ChefDepartementController extends Controller
{
    public function index()
    {
        //obtenir la liste des offres de stage pour chef département
        $monservice = auth()->user()->service;


        $offres = DB::collection('offre_stages')->where('domaine', $monservice)->get();
        return response()->json([
            'status' => 200,
            'offres' => $offres,


        ]);
    }

    public function getEncadrants()
    {
        $nomdep = auth()->user()->departement;

        $encadrants = DB::collection('users')->where('role_id', 'Encadrant')->where('departement', $nomdep)->get();
        return response()->json([
            'status' => 200,
            'encadrants' => $encadrants
        ]);
    }
}
