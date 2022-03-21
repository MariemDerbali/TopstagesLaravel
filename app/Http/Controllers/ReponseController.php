<?php

namespace App\Http\Controllers;

use App\Models\Reponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReponseController extends Controller
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
        //Création de la réponse

        $validator = Validator::make($request->all(), [

            'reponseText',
            'reponseImage',
            'reponseCorrecte' => 'required',
        ]);
        //spécifier la question de la réponse
        $validatorquestionId = Validator::make($request->all(), [
            'questionID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            //vérifier si les champs de réponses ne sont pas vides
            $check1 =  $request->input('reponseText');
            $check2 =  $request->file('reponseImage');

            if ($check1 == null && $check2 == null) {

                return response()->json([
                    'status' => 505,
                    'message' => 'Veuillez préciser le type de réponse',
                ]);
            } else if ($validatorquestionId->fails()) {
                return response()->json([
                    'status' => 423,
                    'message' => "Vous devez d'abord créer une question",
                ]);
            } else {
                //sinon créer la réponse
                $reponse = new Reponse;
                $reponse->reponseText = $request->input('reponseText');
                $reponse->reponseCorrecte = $request->input('reponseCorrecte');
                $reponse->questionID = $request->questionID;

                if ($request->hasFile('reponseImage')) {
                    $file = $request->file('reponseImage');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('img/reponse/', $filename);
                    $reponse->reponseImage = 'img/reponse/' . $filename;
                }

                $reponse->save();


                return response()->json([
                    'status' => 200,
                    'message' => 'Réponse est créée avec succès',
                    'reponse' => $reponse,

                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Reponse::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //afficher le formulaire de la modification de la réponse
        $reponse = $this->show($id);
        if ($reponse) {
            return response()->json([
                'status' => 200,
                'reponse' => $reponse,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucune reponse trouvée',
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
        //Modifier la réponse
        $validator = Validator::make($request->all(), [
            'reponseText',
            'reponseImage',
            'reponseCorrecte' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $reponse = Reponse::find($id);

            if ($reponse) {

                $reponse->reponseText = $request->input('reponseText');
                $reponse->reponseCorrecte = $request->input('reponseCorrecte');

                if ($request->hasFile('reponseImage')) {
                    $file = $request->file('reponseImage');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('img/reponse/', $filename);
                    $reponse->reponseImage = 'img/reponse/' . $filename;
                }

                $reponse->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Mise à jour effectuée avec succès ',

                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Réponse non trouvée',
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
        //Pour supprimer la réponse
        $reponse = Reponse::find($id);
        if ($reponse) {

            $reponsedeleted = $reponse->delete();

            if ($reponsedeleted) {
                return response()->json([
                    'status' => 200,
                    'message' => 'réponse supprimée avec succès'
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => "réponse n'a pas supprimée"
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucune réponse trouvée'
            ]);
        }
    }
}
