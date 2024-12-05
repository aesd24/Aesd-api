<?php

namespace App\Http\Controllers;

use App\Models\Quizz;
use Illuminate\Http\Request;

class QuizzController extends Controller
{
    // Afficher la liste des quizzes
    public function index()
    {
        $quizzes = Quizz::with('propositionsDeReponses')->get(); // Récupérer tous les quizzes avec leurs propositions
        return view('quizzes.index', compact('quizzes')); // Retourner la vue avec les quizzes
    }

    // Afficher le formulaire de création d'un quiz
    public function create()
    {
        return view('quizzes.create'); // Retourner la vue de création
    }

    // Stocker un nouveau quiz
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'intitule' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        // Créer un nouveau quiz
        Quizz::create([
            'intitule' => $request->intitule,
            'date' => $request->date,
        ]);

        return redirect()->route('quizzes.index')->with('success', 'Quiz créé avec succès.');
    }

    // Afficher un quiz spécifique
    public function show(Quizz $quizz)
    {
        return view('quizzes.show', compact('quizz')); // Retourner la vue pour afficher le quiz
    }

    // Afficher le formulaire d'édition d'un quiz
    public function edit(Quizz $quizz)
    {
        return view('quizzes.edit', compact('quizz')); // Retourner la vue d'édition
    }

    // Mettre à jour un quiz
    public function update(Request $request, Quizz $quizz)
    {
        // Valider les données
        $request->validate([
            'intitule' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        // Mettre à jour le quiz
        $quizz->update([
            'intitule' => $request->intitule,
            'date' => $request->date,
        ]);

        return redirect()->route('quizzes.index')->with('success', 'Quiz mis à jour avec succès.');
    }

    // Supprimer un quiz
    public function destroy(Quizz $quizz)
    {
        $quizz->delete(); // Supprimer le quiz

        return redirect()->route('quizzes.index')->with('success', 'Quiz supprimé avec succès.');
    }
}
