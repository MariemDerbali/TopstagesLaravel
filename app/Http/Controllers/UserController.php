<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Obtenir la liste de tous les utilisateurs sauf "Stagiaire"
        $users = User::where('role_id', '!=', 'Stagiaire')->get();

        return response()->json([
            'status' => 200,
            'users' => $users
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
        //Créer un nouvel utilisateur avec un rôle sélectionné sauf "Stagiaire"
        $validator = Validator::make($request->all(), [
            'matricule' => ['required', 'unique:users'],
            'tel' => ['required', 'regex:/^[2459]\d{7}$/'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'adresse' => 'required',
            'role_id' => 'required',
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $user = new User;
            $roleSelected = DB::collection('roles')->where('nom', $request->input('role_id'))->first();

            $user->role_id = $roleSelected['nom'];
            $user->matricule = $request->input('matricule');
            $user->adresse = $request->input('adresse');
            $user->nom = $request->input('nom');
            $user->prenom = $request->input('prenom');
            $user->tel = $request->input('tel');
            $user->cinpasseport = $request->input('cinpasseport');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->etat = 'active';

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('img/user/', $filename);
                $user->image = 'img/user/' . $filename;
            }

            $user->save();


            return response()->json([
                'status' => 200,
                'message' => 'Compte créé avec succès',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Afficher un utilisateur par son id
        return User::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Afficher le formulaire de modification de l'utilisateur spécifié par id. 
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
    {
        //Mettre à jour un utilisateur spécifié par id 
        $validator = Validator::make($request->all(), [
            'tel' => ['required', 'regex:/^[2459]\d{7}$/'],
            'adresse' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {

            $user = User::find($id);
            if ($user) {
                $roleSelected = DB::collection('roles')->where('nom', $request->input('role_id'))->first();
                $user->role_id = $roleSelected['nom'];
                $user->matricule = $request->input('matricule');
                $user->adresse = $request->input('adresse');
                $user->nom = $request->input('nom');
                $user->prenom = $request->input('prenom');
                $user->tel = $request->input('tel');
                $user->cinpasseport = $request->input('cinpasseport');
                $user->email = $request->input('email');
                $user->password = bcrypt($request->input('password'));

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

                $user->etat = $request->input('etat');
                $user->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Compte mis à jour avec succès',
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


    public function GetRoles()
    {
        //Obtenir la liste de tous les rôles sauf  "Stagiaire"
        $roles =  DB::collection('roles')->where('nom', '!=', 'Stagiaire')->get();
        return response()->json([
            'status' => 200,
            'roles' => $roles
        ]);
    }
}
