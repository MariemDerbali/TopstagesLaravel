<?php

namespace App\Http\Controllers;

use App\Models\NotificationDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NotifDocumentsController extends Controller
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
            'message' => 'required',
            'Stagiaire_id' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {
            $emetteurRole = auth()->user()->role_id;
            $emetteurImage = auth()->user()->image;
            $notif = new NotificationDocuments;
            $notif->message = $request->input('message');
            $notif->emetteurRole = $emetteurRole;
            $notif->emetteurImage = $emetteurImage;
            $notif->date = Carbon::now()->toDateTimeString();
            $notif->Stagiaire_id = $request->Stagiaire_id;


            $notif->save();


            return response()->json([
                'status' => 200,
                'message' => 'Message est envoyé avec succès',
                'notif' => $notif,

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

    public function MessagesDocuments()
    {

        $notif = NotificationDocuments::whereNotNull('emetteur')->latest()->first();

        if ($notif) {
            return response()->json([
                'status' => 200,
                'notif' => $notif,

            ]);
        } else {
            return response()->json([
                'status' => 401,

            ]);
        }
    }
}
