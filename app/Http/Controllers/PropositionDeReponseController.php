<?php

namespace App\Http\Controllers;

use App\Models\PropositionDeReponse;
use App\Models\Quizz;
use Illuminate\Http\Request;

class PropositionDeReponseController extends Controller
{
    // Afficher la liste des propositions de réponse
    public function index()
    {
        $propositions = PropositionDeReponse::with('quizz')->get(); // Récupérer toutes les propositions avec leurs quiz
        return view('propositions.index', compact('propositions')); // Retourner la vue avec les propositions
    }

    // Afficher le formulaire de création d'une proposition de réponse
    public function create()
    {
        $quizzes = Quizz::all(); // Récupérer tous les quizzes pour le select
        return view('propositions.create', compact('quizzes')); // Retourner la vue de création
    }

    // Stocker une nouvelle proposition de réponse
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'intitule' => 'required|string|max:255',
            'exact' => 'required|boolean',
            'quiz_id' => 'required|exists:quizzes,id', // Assurez-vous que le quiz existe
        ]);

        // Créer une nouvelle proposition de réponse
        PropositionDeReponse::create([
            'intitule' => $request->intitule,
            'exact' => $request->exact,
            'quiz_id' => $request->quiz_id,
        ]);

        return redirect()->route('propositions.index')->with('success', 'Proposition de réponse créée avec succès.');
    }

    // Afficher une proposition de réponse spécifique
    public function show(PropositionDeReponse $proposition)
    {
        return view('propositions.show', compact('proposition')); // Retourner la vue pour afficher la proposition
    }

    // Afficher le formulaire d'édition d'une proposition de réponse
    public function edit(PropositionDeReponse $proposition)
    {
        $quizzes = Quizz::all(); // Récupérer tous les quizzes pour le select
        return view('propositions.edit', compact('proposition', 'quizzes')); // Retourner la vue d'édition
    }

    // Mettre à jour une proposition de réponse
    public function update(Request $request, PropositionDeReponse $proposition)
    {
        // Valider les données
        $request->validate([
            'intitule' => 'required|string|max:255',
            'exact' => 'required|boolean',
            'quiz_id' => 'required|exists:quizzes,id', // Assurez-vous que le quiz existe
        ]);

        // Mettre à jour la proposition de réponse
        $proposition->update([
            'intitule' => $request->intitule,
            'exact' => $request->exact,
            'quiz_id' => $request->quiz_id,
        ]);

        return redirect()->route('propositions.index')->with('success', 'Proposition de réponse mise à jour avec succès.');
    }

    // Supprimer une proposition de réponse
    public function destroy(PropositionDeReponse $proposition)
    {
        $proposition->delete(); // Supprimer la proposition de réponse

        return redirect()->route('propositions.index')->with('success', 'Proposition de réponse supprimée avec succès.');
    }
}
