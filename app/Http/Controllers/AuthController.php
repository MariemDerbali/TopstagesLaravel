<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validation requests
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {

            //Creating a user with role=Stagiaire
            $role = DB::collection('roles')->where('nom', 'Stagiaire')->first();

            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'cinpasseport' => $request->cinpasseport,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $role['_id']
            ]);


            //creating token for stagaire

            $roleCordi = '';
            $token = $user->createToken($user->email . '_Token', [''])->plainTextToken;


            return response()->json([
                'status' => 200,
                'username' => $user->nom,
                'token' => $token,
                'message' => 'Inscrit!',
                'role' => $roleCordi,
            ]);
        }
    }







    public function login(Request $request)
    {
        //validation requests
        $validator = Validator::make($request->all(), [
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8'],
            'password' => ['required', 'string', 'min:8']

        ]);


        //if validation fails
        if ($validator->fails()) {

            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {

            // get user based on their cinpassport form database
            $user = User::where('cinpasseport', $request->cinpasseport)->first();
            // if user not found based on cinpasseport or password
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'information incorrectes',
                ]);
            } else {
                //cheching if the logged in user has role= coordinateur if yes then creating a token for coordinateur and a role variable
                $role = DB::collection('roles')->where('nom', 'Coordinateur')->first();

                if ($user->role_id == $role['_id']) {
                    $roleCordi = $role['nom'];
                    $token = $user->createToken($user->email . '_CoordinateurToken', ['server:coordinateur'])->plainTextToken;
                } else {
                    //creating a token, role variable for users!= coordinateur
                    $roleCordi = '';
                    $token = $user->createToken($user->email . '_Token', [''])->plainTextToken;
                }

                return response()->json([
                    'status' => 200,
                    'username' => $user->nom,
                    'token' => $token,
                    'message' => 'logged in successfully!',
                    'role' => $roleCordi,
                ]);
            }
        }
    }


    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logged out successfully'
        ]);
    }
}
