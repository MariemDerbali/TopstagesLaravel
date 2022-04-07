<?php

namespace App\Http\Controllers;

use App\Models\Reponse;
use App\Models\Question;
use App\Models\Stagiaire;
use App\Models\Testpsychotechnique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class TestPsychotechniqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexQuestionsReponses(Request $request)
    {

        $domaine = Testpsychotechnique::get()->last()->domaine;
        $type = Testpsychotechnique::get()->last()->type;

        $RandomQuestions = [];

        if ($domaine === "DSI" && $type === "Stage PFE") {
            $randomQuestionsFacile = Question::take(1)->skip(rand(0, 3))->where('niveau', 'Facile')->get();

            foreach ($randomQuestionsFacile as $qf) {
                $RandomQuestions[] = [
                    'question' => $qf,
                    'reponses' => Reponse::where('questionID', $qf->_id)->get(),
                    'reponsecorrecte' => DB::collection('reponses')->where('questionID', $qf->_id)->where('reponseCorrecte', 'Oui')->get()

                ];
            }
            $randomQuestionsMoyenne = Question::take(1)->skip(rand(0, 3))->where('niveau', 'Moyenne')->get();

            foreach ($randomQuestionsMoyenne as $qm) {
                $RandomQuestions[] = [
                    'question' => $qm,
                    'reponses' => Reponse::where('questionID', $qm->_id)->get(),
                    'reponsecorrecte' => DB::collection('reponses')->where('questionID', $qm->_id)->where('reponseCorrecte', 'Oui')->get()

                ];
            }

            $randomQuestionsDifficile = Question::take(2)->skip(rand(0, 3))->where('niveau', 'difficile')->get();
            foreach ($randomQuestionsDifficile as $qd) {
                $RandomQuestions[] = [
                    'question' => $qd,
                    'reponses' => Reponse::where('questionID', $qd->_id)->get(),
                    'reponsecorrecte' => DB::collection('reponses')->where('questionID', $qd->_id)->where('reponseCorrecte', 'Oui')->get()

                ];
            }
        } else if ($domaine === "DSI" && $type === "Stage Perfectionnement") {
            //$randomQuestionsFacile=Question::where('niveau', 'Facile')->random(2)->get();
            // $randomQuestionsMoyenne=Question::where('niveau', 'Moyenne')->random(6)->get();
            //$randomQuestionsDifficile=Question::where('niveau', 'Difficile')->random(4)->get();

        } else if ($domaine === "DSI" && $type === "Stage initiaion") {
            //  $randomQuestionsFacile=Question::where('niveau', 'Facile')->random(6)->get();
            // $randomQuestionsMoyenne=Question::where('niveau', 'Moyenne')->random(4)->get();
            // $randomQuestionsDifficile=Question::where('niveau', 'Difficile')->random(2)->get();
        }


        $id = auth()->user()->_id;
        $stagiaire = Stagiaire::find($id);

        if ($RandomQuestions && $stagiaire) {
            return response()->json([
                'status' => 200,
                'questionsreponses' => $RandomQuestions,
                'stagiaire' => $stagiaire

            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Pas de questions trouvées ou stagiaire'
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
