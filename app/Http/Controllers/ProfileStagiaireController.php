<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProfileStagiaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Afficher un stagiaire par son id
        return Stagiaire::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { //Afficher le formulaire de modification de profil pour le stagiaire
        $user = $this->show($id);
        if ($user) {
            return response()->json([
                'status' => 200,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucun utilisateur trouvé',
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
    { //Mettre à jour le profil de stagiaire
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'tel' => ['required', 'regex:/^[2459]\d{7}$/'],
            'adresse' => 'required',
            'role_id' => 'required',
            'cinpasseport' => 'required',
            'description' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {

            $user = Stagiaire::find($id);
            if ($user) {

                $user->adresse = $request->input('adresse');
                $user->nom = $request->input('nom');
                $user->prenom = $request->input('prenom');
                $user->tel = $request->input('tel');
                $user->description = $request->input('description');
                $user->email = $request->input('email');
                $user->cinpasseport = $request->input('cinpasseport');


                if ($request->hasFile('image')) {
                    $path = $user->image;
                    if (File::exists($path)) {

                        File::delete($path);
                    }
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('img/user/', $filename);
                    $user->image = 'img/user/' . $filename;
                }

                $user->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Profil mis à jour avec succès',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Utilisateur non trouvé',
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
