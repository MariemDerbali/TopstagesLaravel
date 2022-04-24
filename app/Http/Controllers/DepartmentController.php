<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class DepartmentController extends Controller
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
            'nomdep' => 'required',
            'chefdep' => 'required'
        ]);

        $validatordirectionId = Validator::make($request->all(), [
            'direction' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else if ($validatordirectionId->fails()) {
            return response()->json([
                'status' => 423,
                'message' => "Vous devez d'abord créer une direction",
            ]);
        } else {
            $Dep = new Department;
            $Dep->nomdep = $request->input('nomdep');
            $Dep->chefdep = $request->input('chefdep');
            $Dep->direction = $request->direction;
            $Dep->etat = 'active';
            $Dep->save();
            return response()->json([
                'status' => 200,
                'message' => 'Département est créé avec succès',
                'Department' => $Dep,

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
        return Department::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $dep = $this->show($id);
        if ($dep) {
            return response()->json([
                'status' => 200,
                'dep' => $dep,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucun département trouvé',
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
        //Mettre à jour un département
        $validator = Validator::make($request->all(), [
            'nomdep' => ['required', 'string', 'max:255'],
            'chefdep' => ['required', 'string', 'max:255']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $dep = Department::find($id);

            if ($dep) {

                $dep->nomdep = $request->input('nomdep');
                $dep->chefdep = $request->input('chefdep');
                $dep->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Département mis à jour avec succès',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Département non trouvé',
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

    public function desactiverDepartement($id)
    {
        $dep  = Department::find($id);
        if ($dep) {
            if ($dep->etat == 'active') {
                $dep->etat = 'inactive';
                $dep->save();
                return response()->json([
                    'status' => 200,
                    'message' => 'Département est désactivé'
                ]);
            } else {
                $dep->etat = 'active';
                $dep->save();
                return response()->json([
                    'status' => 201,
                    'message' => 'Département est activé'
                ]);
            }
        } else {
            return response()->json([
                'status' => 401,
                'message' => "Département non trouvé"
            ]);
        }
    }
}
