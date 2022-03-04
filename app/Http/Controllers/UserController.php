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

        $roleStagiaire = DB::collection('roles')->where('nom', 'Stagiaire')->first();
        $roleStagiaireId = $roleStagiaire['nom'];
        $allusersexcpetstagiaire = User::where('role_id', '!=', $roleStagiaireId)->get();


        return response()->json([
            'status' => 200,
            'users' => $allusersexcpetstagiaire
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

        //$data = $request->all();

        //$data['password'] = bcrypt($request->password);
        // $data['role_id']=


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

            //  $user = User::create($data);
            return response()->json([
                //    'user' => $user,
                'status' => 200,
                'message' => 'User created ',
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
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'status' => 200,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No user found',
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
                    'message' => 'User updated ',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'User not found ',
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
        $roles =  DB::collection('roles')->where('nom', '!=', 'Stagiaire')->get();
        return response()->json([
            'status' => 200,
            'roles' => $roles
        ]);
    }
}
