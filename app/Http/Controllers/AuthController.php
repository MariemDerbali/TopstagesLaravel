<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as RulesPassword;

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
                'role_id' => $role['nom']
            ]);
            $roleCordi = '';
            $token = $user->createToken($user->email . '_Token', [''])->plainTextToken;
            return response()->json([
                'status' => 200,
                'username' => $user->nom,
                'token' => $token,
                'message' => 'Votre compte a été créé',
                'role' => $roleCordi,
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

            $user = User::where('cinpasseport', $request->cinpasseport)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Informations incorrectes',
                ]);
            } else {

                if (($user->role_id != 'Stagiaire') && ($user->first_time_login == '')) {

                    return response()->json([
                        'status' => 204,
                        'user_id' => $user->_id,
                    ]);
                } else if (($user->first_time_login !== '')) {

                    $roleCordinateur = DB::collection('roles')->where('nom', 'Coordinateur')->first();
                    $roleServiceFormation = DB::collection('roles')->where('nom', 'ServiceFormation')->first();

                    if ($user->role_id == $roleCordinateur['nom']) {
                        $role = $roleCordinateur['nom'];
                        $token = $user->createToken($user->email . '_CoordinateurToken', ['server:coordinateur'])->plainTextToken;
                    } else if ($user->role_id == $roleServiceFormation['nom']) {
                        $role = $roleServiceFormation['nom'];
                        $token = $user->createToken($user->email . '_ServiceFormationToken', ['server:serviceformation'])->plainTextToken;
                    } else {
                        $role = '';
                        $token = $user->createToken($user->email . '_Token', [''])->plainTextToken;
                    }

                    return response()->json([
                        'status' => 200,
                        'username' => $user->nom,
                        'token' => $token,
                        'message' => 'Connecté avec succès!',
                        'role' => $role,
                    ]);
                }
            }
        }
    }


    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Vous avez été déconnecté avec succès'
        ]);
    }


    public function getCurrentUser()
    {
        $id = auth()->user()->_id;
        $currentuser = User::find($id);
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


    public function forgotpassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        } else {

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Aucun utilisateur trouvé'
                ]);
            } else {
                $status = Password::sendResetLink($request->only('email'));

                if ($status == Password::RESET_LINK_SENT) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'E-mail envoyé avec succès'
                    ]);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => "E-mail n'a pas été envoyé "
                    ]);
                }
            }
        }
    }


    public function resetforgottenpassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => ['required', 'string', 'confirmed', 'min:8', RulesPassword::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        } else {
            $status = Password::reset(
                $request->only('password', 'password_confirmation', 'token'),
                function ($user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    $user->tokens()->delete();
                    event(new PasswordReset($user));
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Mot de passe réinitialisé avec succès'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => "Mot de passe n'a pas été réinitialisé"
                ]);
            }
        }
    }


    public function resetfirstloginpassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'confirmed', 'min:8', RulesPassword::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        } else {
            $user = User::find($id);
            if ($user) {
                $user->password = bcrypt($request->password);
                $user->first_time_login = Carbon::now()->toDateTimeString();
                $user->update();
                return response()->json([
                    'status' => 200,
                    'message' => 'Mot de passe réinitialisé avec succès',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Aucun utilisateur trouvé',
                ]);
            }
        }
    }
}
