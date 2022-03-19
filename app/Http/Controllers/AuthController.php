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
    //Pour s'inscrire
    public function register(Request $request)
    {

        //validation des requêtes
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);
        //Si la validation échoue, une réponse d'erreur sera renvoyée
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {

            //enregistrement de l'utilisateur avec le rôle de stagiaire puisque seul le stagiaire peut s'inscrire
            $role = DB::collection('roles')->where('nom', 'Stagiaire')->first();
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'cinpasseport' => $request->cinpasseport,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $role['nom']
            ]);

            //Pour sécuriser une API il faut une authentification des utilisateurs. Laravel en propose une par défaut. On assigne à chaque utilisateur inscrit un token aléatoire. 
            //Ce token est ensuite utilisé pour chaque requête.
            $roleCordi = '';
            //création de Token pour le stagiaire 
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


    //Pour s'authentifier
    public function login(Request $request)
    {

        //validation des requêtes
        $validator = Validator::make($request->all(), [
            'cinpasseport' => ['required', 'string', 'min:7', 'max:8'],
            'password' => ['required', 'string', 'min:8']

        ]);

        //Si la validation échoue, une réponse d'erreur sera renvoyée
        if ($validator->fails()) {

            return response()->json([
                'validation_errors' => $validator->messages(),
            ]);
        } else {
            //vérification des données saisies
            $user = User::where('cinpasseport', $request->cinpasseport)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Informations incorrectes',
                ]);
            } else {
                //vérification de la première connexion et si le rôle est stagiaire
                if (($user->role_id != 'Stagiaire') && ($user->first_time_login == '')) {

                    return response()->json([
                        'status' => 204,
                        'user_id' => $user->_id,
                    ]);
                }
                //vérification de la première connexion et si le rôle est différent de stagiaire
                else if (($user->first_time_login !== '')) {

                    $roleCordinateur = DB::collection('roles')->where('nom', 'Coordinateur')->first();
                    $roleServiceFormation = DB::collection('roles')->where('nom', 'ServiceFormation')->first();
                    //création de token pour le coordinateur
                    if ($user->role_id == $roleCordinateur['nom']) {
                        $role = $roleCordinateur['nom'];
                        $token = $user->createToken($user->email . '_CoordinateurToken', ['server:coordinateur'])->plainTextToken;
                    } else if ($user->role_id == $roleServiceFormation['nom']) {
                        //création de token pour le service formation
                        $role = $roleServiceFormation['nom'];
                        $token = $user->createToken($user->email . '_ServiceFormationToken', ['server:serviceformation'])->plainTextToken;
                    } else {
                        //création de token pour l'encadrant et le chéf de département (BACK SOON!)
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

    //Pour se déconnecter
    public function logout()
    {
        //Pour effacer l'entrée de cache de token associée au compte qui s'est déconnecté
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Vous avez été déconnecté avec succès'
        ]);
    }


    //Pour obtenir l'utilisateur actuellement connecté
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


    //Pour récupérer le mot de passe oublié
    public function forgotpassword(Request $request)
    {

        //validation des requêtes
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        //Si la validation échoue, une réponse d'erreur sera renvoyée
        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        } else {
            //vérification de l'utilisateur
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Aucun utilisateur trouvé'
                ]);
            } else {
                //envoi d'un lien de vérification par e-mail
                $status = Password::sendResetLink($request->only('email'));

                if ($status == Password::RESET_LINK_SENT) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'E-mail envoyé avec succès'
                    ]);
                } else {
                    //si l'e-mail n'a pas été envoyé , une réponse d'erreur sera renvoyée
                    return response()->json([
                        'status' => 404,
                        'message' => "E-mail n'a pas été envoyé "
                    ]);
                }
            }
        }
    }

    //Changement de mot passe oublié
    public function resetforgottenpassword(Request $request)
    {
        //validation des requêtes
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => ['required', 'string', 'confirmed', 'min:8', RulesPassword::defaults()],
        ]);

        //Si la validation échoue, une réponse d'erreur sera renvoyée
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

    //Changement de mot de passe lors de la première connexion
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
                //enregistrement la date de la première connexion
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
