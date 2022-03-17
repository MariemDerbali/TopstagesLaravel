<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::all();

        return response()->json([
            'status' => 200,
            'questions' => $questions,
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
                $id = $question->_id;


                return response()->json([
                    'status' => 200,
                    'message' => 'Question est créée avec succès',
                    'questionId' => $id

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
        return Question::where('_id', $id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $question = $this->show($id);
        if ($question) {
            return response()->json([
                'status' => 200,
                'question' => $question,
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Aucune question trouvée',
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
            $question = Question::find($id);

            if ($question) {

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

                $question->etat = $request->input('etat');

                $question->update();
                // $id = $question->_id;

                return response()->json([
                    'status' => 200,
                    'message' => 'Question mise à jour avec succès ',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Question non trouvée',
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

    public function getReponses($id)
    {
        $reponses = DB::collection('reponses')->where('questionID', $id)->get();

        return response()->json([
            'status' => 200,
            'reponses' => $reponses
        ]);
    }
}
