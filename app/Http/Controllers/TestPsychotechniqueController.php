<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Reponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TestPsychotechniqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexQuestionsFacile()
    {
        //baaed nzidou fazet random lena
        $questions = Question::where('niveau', 'Facile')->get();

        $reponses = [];
        foreach ($questions as $question) {
            $reponses = array_merge($reponses, array(Reponse::where('questionID', $question->_id)->get()));
        };




        if ($questions) {
            return response()->json([
                'status' => 200,
                'questions' => $questions,
                'reponses' => $reponses
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Pas de questions trouvées'
            ]);
        }
    }

    public function indexQuestionsMoyenne()
    {
        //baaed nzidou fazet random lena
        $questions = Question::where('niveau', 'Moyenne')->get();

        foreach ($questions as $question) {
            $reponses = Reponse::where('questionID', $question->_id)->get();
        }

        if ($questions) {
            return response()->json([
                'status' => 200,
                'questions' => $questions,
                'reponses' => $reponses

            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Pas de questions trouvées'
            ]);
        }
    }

    public function indexQuestionsDifficile()
    {
        //baaed nzidou fazet random lena
        $questions = Question::where('niveau', 'Difficile')->get();

        foreach ($questions as $question) {
            $reponses = Reponse::where('questionID', $question->_id)->get();
        }

        if ($questions) {
            return response()->json([
                'status' => 200,
                'questions' => $questions,
                'reponses' => $reponses

            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Pas de questions trouvées'
            ]);
        }
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
        //
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
