<?php

namespace App\Http\Controllers;

use App\Models\Reponse;
use App\Models\Question;
use App\Models\Stagiaire;
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

        $questionsreponses = [];

        foreach ($questions as $question) {
            $questionsreponses[] = [
                'question' => $question,
                'reponses' => Reponse::where('questionID', $question->_id)->get(),
                'reponsecorrecte' => DB::collection('reponses')->where('questionID', $question->_id)->where('reponseCorrecte', 'Oui')->get()

            ];
        };
        $id = auth()->user()->_id;
        $stagiaire = Stagiaire::find($id);

        if ($questions && $stagiaire) {
            return response()->json([
                'status' => 200,
                'questionsreponses' => $questionsreponses,
                'stagiaire' => $stagiaire

            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Pas de questions trouvées ou stagiaire'
            ]);
        }
    }

    public function indexQuestionsMoyenne()
    {
        //baaed nzidou fazet random lena
        $questions = Question::where('niveau', 'Moyenne')->get();

        $questionsreponses = [];

        foreach ($questions as $question) {
            $questionsreponses[] = [
                'question' => $question,
                'reponses' => Reponse::where('questionID', $question->_id)->get()

            ];
        };

        if ($questions) {
            return response()->json([
                'status' => 200,
                'questionsreponses' => $questionsreponses

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

        $questionsreponses = [];

        foreach ($questions as $question) {
            $questionsreponses[] = [
                'question' => $question,
                'reponses' => Reponse::where('questionID', $question->_id)->get()

            ];
        };

        if ($questions) {
            return response()->json([
                'status' => 200,
                'questionsreponses' => $questionsreponses

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
