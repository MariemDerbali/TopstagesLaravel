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
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {

            $role = DB::collection('roles')->where('nom', 'Stagiaire')->first();
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'cinpasseport' => $request->cinpasseport,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $role['_id']
            ]);



            $token = $user->createToken($user->email . '_Token')->plainTextToken;


            return response()->json([
                'status' => 200,
                'username' => $user->nom,
                'token' => $token,
                'message' => 'Inscrit!'
            ]);
        }
    }



    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8'],
            'password' => ['required', 'string', 'min:8']

        ]);

        if ($validator->fails()) {

            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {

            // Check cinpasseport
            $user = User::where('cinpasseport', $request->cinpasseport)->first();
            // Check password
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'information incorrectes',
                ]);
            } else {

                $role = DB::collection('roles')->where('nom', 'Coordinateur')->first();

                if ($user->role_id == $role['_id']) {
                    $token = $user->createToken($user->email . '_CoordinateurToken', ['server:coordinateur'])->plainTextToken;
                } else {
                    $token = $user->createToken($user->email . '_Token', [''])->plainTextToken;
                }

                return response()->json([
                    'status' => 200,
                    'username' => $user->nom,
                    'token' => $token,
                    'message' => 'logged in successfully!',
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
