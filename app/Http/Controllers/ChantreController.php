<?php

namespace App\Http\Controllers;

use App\Models\Chantre;
use App\Models\Church;
use App\Models\SujetDeDiscussion;
use Illuminate\Http\Request;

class ChantreController extends Controller
{
    /**
     * Afficher la liste des chantres.
     */
    public function index()
    {
        $chantres = Chantre::with(['user', 'church'])->get();
        return view('chantres.index', compact('chantres'));
    }

    /**
     * Afficher le formulaire de création d'un nouveau chantre.
     */
    public function create()
    {
        $churches = Church::all(); // Récupère toutes les églises
        $sujets = SujetDeDiscussion::all(); // Récupère tous les sujets de discussion
        return view('chantres.create', compact('churches', 'sujets'));
    }

    /**
     * Enregistrer un nouveau chantre.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'manager' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'church_id' => 'required|exists:churches,id',
            'sujets_de_discussion' => 'nullable|array', // S'assurer que les sujets sont un tableau
            'sujets_de_discussion.*' => 'exists:sujets_de_discussion,id', // Validation pour chaque sujet
            'comments' => 'nullable|array',
            'comments.*' => 'string', // Validation pour chaque commentaire associé
        ]);

        $chantre = Chantre::create($validatedData);

        // Attacher les sujets de discussion avec leurs commentaires
        if (isset($request->sujets_de_discussion)) {
            foreach ($request->sujets_de_discussion as $index => $sujet_id) {
                $chantre->sujetsDeDiscussion()->attach($sujet_id, [
                    'Comment' => $request->comments[$index] ?? null
                ]);
            }
        }

        return redirect()->route('chantres.index')->with('success', 'Chantre créé avec succès.');
    }

    /**
     * Afficher les détails d'un chantre.
     */
    public function show(Chantre $chantre)
    {
        return view('chantres.show', compact('chantre'));
    }

    /**
     * Afficher le formulaire d'édition d'un chantre.
     */
    public function edit(Chantre $chantre)
    {
        $churches = Church::all(); // Récupère toutes les églises
        $sujets = SujetDeDiscussion::all(); // Récupère tous les sujets de discussion
        return view('chantres.edit', compact('chantre', 'churches', 'sujets'));
    }

    /**
     * Mettre à jour les informations d'un chantre.
     */
    public function update(Request $request, Chantre $chantre)
    {
        $validatedData = $request->validate([
            'manager' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'church_id' => 'required|exists:churches,id',
            'sujets_de_discussion' => 'nullable|array',
            'sujets_de_discussion.*' => 'exists:sujets_de_discussion,id',
            'comments' => 'nullable|array',
            'comments.*' => 'string',
        ]);

        $chantre->update($validatedData);

        // Synchroniser les sujets de discussion avec leurs commentaires
        $chantre->sujetsDeDiscussion()->detach(); // Détache les sujets actuels
        if (isset($request->sujets_de_discussion)) {
            foreach ($request->sujets_de_discussion as $index => $sujet_id) {
                $chantre->sujetsDeDiscussion()->attach($sujet_id, [
                    'Comment' => $request->comments[$index] ?? null
                ]);
            }
        }

        return redirect()->route('chantres.index')->with('success', 'Chantre mis à jour avec succès.');
    }

    /**
     * Supprimer un chantre.
     */
    public function destroy(Chantre $chantre)
    {
        $chantre->sujetsDeDiscussion()->detach(); // Détache les relations de sujets de discussion
        $chantre->delete();

        return redirect()->route('chantres.index')->with('success', 'Chantre supprimé avec succès.');
    }
}
