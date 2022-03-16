<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Question;

class QuestionController extends Controller
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

            'questionText',
            'questionImage',
            'duree' => 'required',
            'niveau' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages(),
            ]);
        } else {


            $check1 =  $request->input('questionText');
            $check2 =  $request->file('questionImage');

            if ($check1 == null && $check2 == null) {

                return response()->json([
                    'status' => 505,
                    'message' => 'Les champs des questions sont obligatoires',
                ]);
            } else {
                $question = new Question;
                $question->questionText = $request->input('questionText');
                $question->duree = $request->input('duree');
                $question->niveau = $request->input('niveau');

                if ($request->hasFile('questionImage')) {
                    $file = $request->file('questionImage');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('img/question/', $filename);
                    $question->questionImage = 'img/question/' . $filename;
                }

                $question->etat = 'active';

                $question->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Question est créée avec succès',

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
