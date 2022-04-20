<?php

namespace App\Http\Controllers;

use App\Models\Critere;
use App\Models\Reponse;
use App\Models\Question;
use App\Models\Stagiaire;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\DemandeStage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;



class DemandestageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexQuestionsReponses(Request $request)
    {
        //le domaine et le type de stage sélectionné par le stagiaire
        $postID = DemandeStage::get()->last()->_id;
        $domaine = DemandeStage::get()->last()->domaine;
        $type = DemandeStage::get()->last()->type;

        //obtenir le critère correspondant au domaine et au type de stage
        $critere = Critere::where('typestage', $type)->where('domainestage', $domaine)->where('etat', 'active')->first();

        //s'il est trouvé
        if ($critere) {

            $duree = 0; //durée test
            $RandomQuestions = []; //array de de questions

            $nbrquestionsfaciles = $critere->nombrequestionsfaciles;
            $nbrquestionsmoyennes = $critere->nombrequestionsmoyennes;
            $nbrquestionsdifficiles = $critere->nombrequestionsdifficiles;

            $notequestionfacile = $critere->notequestionfacile * $nbrquestionsfaciles;
            $notequestionmoyenne = $critere->notequestionmoyenne * $nbrquestionsmoyennes;
            $notequestiondifficile = $critere->notequestiondifficile * $nbrquestionsdifficiles;

            $notetotale = $notequestionfacile + $notequestionmoyenne + $notequestiondifficile; //note test


            $randomQuestionsFacile = Question::where('niveau', 'Facile')->get()->random($nbrquestionsfaciles);

            foreach ($randomQuestionsFacile as $qf) {
                $duree = $duree + $qf->duree;

                $RandomQuestions[] = [
                    'question' => $qf,
                    'reponses' => Reponse::where('questionID', $qf->_id)->where('etat', 'active')->get(),
                    'reponsecorrecte' => DB::collection('reponses')->where('questionID', $qf->_id)->where('reponseCorrecte', 'Oui')->get()->where('etat', 'active'),


                ];
            }

            $randomQuestionsMoyenne = Question::where('niveau', 'Moyenne')->get()->random($nbrquestionsmoyennes);

            foreach ($randomQuestionsMoyenne as $qm) {
                $duree = $duree + $qm->duree;

                $RandomQuestions[] = [
                    'question' => $qm,
                    'reponses' => Reponse::where('questionID', $qm->_id)->where('etat', 'active')->get(),
                    'reponsecorrecte' => DB::collection('reponses')->where('questionID', $qm->_id)->where('reponseCorrecte', 'Oui')->where('etat', 'active')->get(),

                ];
            }

            $randomQuestionsDifficile = Question::where('niveau', 'difficile')->get()->random($nbrquestionsdifficiles);

            foreach ($randomQuestionsDifficile as $qd) {
                $duree = $duree + $qd->duree;
                $RandomQuestions[] = [
                    'question' => $qd,
                    'reponses' => Reponse::where('questionID', $qd->_id)->where('etat', 'active')->get(),
                    'reponsecorrecte' => DB::collection('reponses')->where('questionID', $qd->_id)->where('reponseCorrecte', 'Oui')->where('etat', 'active')->get(),


                ];
            }



            $id = auth()->user()->_id;
            $stagiaire = Stagiaire::find($id);

            if ($RandomQuestions && $stagiaire) {
                return response()->json([
                    'status' => 200,
                    'questionsreponses' => $RandomQuestions,
                    'duree' => $duree,
                    'notetotale' => $notetotale,
                    'stagiaire' => $stagiaire,
                    'postid' => $postID,


                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Pas de questions trouvées ou stagiaire'
                ]);
            }
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Pas de critère trouvé'
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

        $post = DemandeStage::find($id);

        if ($post) {


            if ($request->hasFile('ficherep') && $request->hasFile('cv')) {
                $pathFiche = $post->ficherep;
                $pathCV = $post->cv;

                if (File::exists($pathFiche) || File::exists($pathCV)) {

                    File::delete($pathFiche);
                    File::delete($pathCV);
                }
                $fileFiche = $request->file('ficherep');
                $fileCV = $request->file('cv');
                $extensionFiche = $fileFiche->getClientOriginalExtension();
                $extensionCV = $fileCV->getClientOriginalExtension();

                $filenameFiche = Str::random(5) . '.' . $extensionFiche;
                $filenameCV = Str::random(5) . '.' . $extensionCV;
                $fileFiche->move('img/post/', $filenameFiche);
                $fileCV->move('img/post/', $filenameCV);

                $post->ficherep = 'img/post/' . $filenameFiche;
                $post->cv = 'img/post/' . $filenameCV;
                $post->date = Carbon::now()->toDateTimeString();
            }

            $post->etatpost = 'published';
            $post->etatdemande = 'Nouvellement créé';


            $post->update();

            return response()->json([
                'status' => 200,
                'message' => 'Vous avez postulé avec succès',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Post non trouvé',
            ]);
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


    public function monPost($id)
    {
        $post =  DemandeStage::where('_id', $id)->first();
        return response()->json([
            'status' => 200,
            'post' => $post
        ]);
    }

    public function SuivreMonDossier()
    {
        $id = auth()->user()->_id;
        $currentuser = Stagiaire::find($id);

        $demandesdestages = DemandeStage::where('stagiaire.stagiaireId', $currentuser->id)->where('etatpost', 'published')->get();

        return response()->json([
            'status' => 200,
            'dossier' => $demandesdestages,

        ]);
    }
}
