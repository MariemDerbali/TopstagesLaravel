<?php

namespace App\Http\Controllers;

use App\Models\Critere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CritereController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $criteres = Critere::all();

        return response()->json([
            'status' => 200,
            'criteres' => $criteres
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
            'typestage' => 'required',
            'domainestage' => 'required',
            'nombrequestionsfaciles' => 'required',
            'nombrequestionsmoyennes' => 'required',
            'nombrequestionsdifficiles' => 'required',
            'notequestionfacile' => 'required',
            'notequestionmoyenne' => 'required',
            'notequestiondifficile' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $critere = new Critere;
            $critere->typestage = $request->input('typestage');
            $critere->domainestage = $request->input('domainestage');
            $critere->nombrequestionsfaciles = $request->input('nombrequestionsfaciles');
            $critere->nombrequestionsmoyennes = $request->input('nombrequestionsmoyennes');
            $critere->nombrequestionsdifficiles = $request->input('nombrequestionsdifficiles');
            $critere->notequestionfacile = $request->input('notequestionfacile');
            $critere->notequestionmoyenne = $request->input('notequestionmoyenne');
            $critere->notequestiondifficile = $request->input('notequestiondifficile');
            $critere->etat = 'active';

            $critere->save();
            return response()->json([
                'status' => 200,
                'message' => 'Critère créé avec succès',
                'critere' => $critere
            ]);
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
        return Critere::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $critere = $this->show($id);
        if ($critere) {
            return response()->json([
                'status' => 200,
                'critere' => $critere,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucun critère trouvé',
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
            'typestage' => 'required',
            'domainestage' => 'required',
            'nombrequestionsfaciles' => 'required',
            'nombrequestionsmoyennes' => 'required',
            'nombrequestionsdifficiles' => 'required',
            'notequestionfacile' => 'required',
            'notequestionmoyenne' => 'required',
            'notequestiondifficile' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $critere = Critere::find($id);

            if ($critere) {

                $critere->typestage = $request->input('typestage');
                $critere->domainestage = $request->input('domainestage');
                $critere->nombrequestionsfaciles = $request->input('nombrequestionsfaciles');
                $critere->nombrequestionsmoyennes = $request->input('nombrequestionsmoyennes');
                $critere->nombrequestionsdifficiles = $request->input('nombrequestionsdifficiles');
                $critere->notequestionfacile = $request->input('notequestionfacile');
                $critere->notequestionmoyenne = $request->input('notequestionmoyenne');
                $critere->notequestiondifficile = $request->input('notequestiondifficile');

                $critere->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Critère mis à jour avec succès',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Critère non trouvé',
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


    public function getDirections()
    {
        $deps =  DB::collection('departments')->where('etat', 'active')->get();
        return response()->json([
            'status' => 200,
            'deps' => $deps
        ]);
    }
    public function desactiverCritere($id)
    {
        $critere = Critere::find($id);
        if ($critere) {
            if ($critere->etat == 'active') {
                $critere->etat = 'inactive';
                $critere->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Critère est désactivé'
                ]);
            } else {
                $critere->etat = 'active';
                $critere->save();
                return response()->json([
                    'status' => 201,
                    'message' => 'Critère est activé'
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Critère non trouvé"
            ]);
        }
    }
}
