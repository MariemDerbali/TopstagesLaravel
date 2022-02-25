<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);

        $role = DB::collection('roles')->where('nom', 'Stagiaire')->first();
        $user = User::create([
            'nom' => $fields['nom'],
            'prenom' => $fields['prenom'],
            'cinpasseport' => $fields['cinpasseport'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role_id' => $role['_id']
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }



    public function login(Request $request)
    {
        $fields = $request->validate([
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8'],
            'password' => ['required', 'string', 'min:8']

        ]);

        // Check cinpasseport
        $user = User::where('cinpasseport', $fields['cinpasseport'])->first();
        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return  response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }


    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
