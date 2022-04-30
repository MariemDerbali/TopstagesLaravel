<?php

namespace App\Http\Controllers;

use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReunionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reunions = Reunion::all();

        return response()->json([
            'status' => 200,
            'reunions' => $reunions,

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

            'title' => 'required',
            'url' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {

            $reunion = new Reunion;
            $reunion->title = $request->input('title');
            $reunion->url = $request->input('url');
            $reunion->start = Date("Y-m-d H:i", strtotime(current(explode("(", $request->input('start'))) . '+1 hour'));
            $reunion->end = Date("Y-m-d H:i", strtotime(current(explode("(", $request->input('end'))) . '+1 hour'));

            $reunion->save();

            return response()->json([
                'status' => 200,
                'message' => 'Réunion est créée avec succès',

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
        return Reunion::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reunion = $this->show($id);
        if ($reunion) {
            return response()->json([
                'status' => 200,
                'reunion' => $reunion,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucune réunion trouvée',
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
            'title' => 'required',
            'url' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $reunion = Reunion::find($id);

            if ($reunion) {

                $reunion->title = $request->input('title');
                $reunion->url = $request->input('url');
                $reunion->start = Date("Y-m-d H:i", strtotime(current(explode("(", $request->input('start'))) . '+1 hour'));
                $reunion->end = Date("Y-m-d H:i", strtotime(current(explode("(", $request->input('end'))) . '+1 hour'));


                $reunion->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Mise à jour effectuée avec succès',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Réunion non trouvée',
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
        $reunion  = Reunion::find($id);
        if ($reunion) {
            $reunion->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Réunion est annulée'
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Réunion non trouvée"
            ]);
        }
    }
}
