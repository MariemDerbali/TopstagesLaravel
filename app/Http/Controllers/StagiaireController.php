<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use Illuminate\Http\Request;

class StagiaireController extends Controller
{
    //Pour obtenir le stagiaire actuellement connecté
    public function getCurrentStagiaire()
    {
        $id = auth()->user()->_id;
        $currentuser = Stagiaire::find($id);
        if ($currentuser) {
            return response()->json([
                'status' => 200,
                'currentuser' => $currentuser
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucun utilisateur trouvé'
            ]);
        }
    }
}
