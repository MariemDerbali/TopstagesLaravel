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


        $validator = Validator::make($request->all(), [

            'reponseText',
            'reponseImage',
            'reponseCorrecte' => 'required',
        ]);

        $validatorquestionId = Validator::make($request->all(), [
            'questionID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $check1 =  $request->input('reponseText');
            $check2 =  $request->file('reponseImage');

            if ($check1 == null && $check2 == null) {

                return response()->json([
                    'status' => 505,
                    'message' => 'Les champs des réponses sont obligatoires',
                ]);
            } else if ($validatorquestionId->fails()) {
                return response()->json([
                    'status' => 423,
                    'message' => "Vous devez d'abord créer une question",
                ]);
            } else {
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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