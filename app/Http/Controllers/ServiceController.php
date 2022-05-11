<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ServiceController extends Controller
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
            'nomService'  => ['required', 'string', 'max:255'],

        ]);

        $validatordepartementId = Validator::make($request->all(), [
            'departement' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else if ($validatordepartementId->fails()) {
            return response()->json([
                'status' => 423,
                'message' => "Vous devez d'abord créer un département",
            ]);
        } else {
            $service = new Service;
            $service->nomService = $request->input('nomService');
            $service->departement = $request->departement;
            $service->etat = 'active';
            $service->save();
            return response()->json([
                'status' => 200,
                'message' => 'Service est créé avec succès',
                'Service' => $service,

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
        return Service::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = $this->show($id);
        if ($service) {
            return response()->json([
                'status' => 200,
                'service' => $service,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucun service trouvé',
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
            'nomService' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $service = Service::find($id);

            if ($service) {

                $service->nomService = $request->input('nomService');
                $service->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Service mis à jour avec succès',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Service non trouvé',
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

    public function desactiverService($id)
    {
        $service  = Service::find($id);
        if ($service) {
            if ($service->etat == 'active') {
                $service->etat = 'inactive';
                $service->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Service est désactivé'
                ]);
            } else {
                $service->etat = 'active';
                $service->save();
                return response()->json([
                    'status' => 201,
                    'message' => 'Service est activé'
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Service non trouvé"
            ]);
        }
    }
}
