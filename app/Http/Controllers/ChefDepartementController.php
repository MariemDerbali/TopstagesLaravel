<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ChefDepartementController extends Controller
{
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
