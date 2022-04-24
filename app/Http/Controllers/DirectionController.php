<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DirectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $directions = Direction::all();

        return response()->json([
            'status' => 200,
            'directions' => $directions,
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

            'nomdirection' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {


            $direction = new Direction;
            $direction->nomdirection = $request->input('nomdirection');


            $direction->etat = 'active';

            $direction->save();
            $id = $direction->_id;


            return response()->json([
                'status' => 200,
                'message' => 'Direction est créée avec succès',
                'DirectionId' => $id

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
        return Direction::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $direction = $this->show($id);
        if ($direction) {
            return response()->json([
                'status' => 200,
                'direction' => $direction,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucune direction trouvée',
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
            'nomdirection' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $direction = Direction::find($id);

            if ($direction) {

                $direction->nomdirection = $request->input('nomdirection');

                $direction->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Mise à jour effectuée avec succès',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Direction non trouvée',
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

    public function getdepartements($id)
    {
        $deps = DB::collection('departments')->where('direction', $id)->get();

        return response()->json([
            'status' => 200,
            'departements' => $deps
        ]);
    }



    public function desactiverDirection($id)
    {
        $direction  = Direction::find($id);
        if ($direction) {
            if ($direction->etat == 'active') {
                $direction->etat = 'inactive';
                $direction->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Direction est désactivée'
                ]);
            } else {
                $direction->etat = 'active';
                $direction->save();
                return response()->json([
                    'status' => 201,
                    'message' => 'Direction est activée'
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Direction non trouvée"
            ]);
        }
    }
}
