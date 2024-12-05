<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use Illuminate\Http\Request;

class ActualiteController extends Controller
{
    /**
     * Affiche la liste des actualités.
     */
    public function index()
    {
        $actualites = Actualite::all(); // Récupérer toutes les actualités
        return view('actualites.index', compact('actualites'));
    }

    /**
     * Affiche le formulaire de création d'une actualité.
     */
    public function create()
    {
        return view('actualites.create');
    }

    /**
     * Enregistre une nouvelle actualité.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'date_publication' => 'required|date',
            'date_expiration' => 'nullable|date|after:date_publication',
            'tags' => 'nullable|string',
        ]);

        Actualite::create($request->all());

        return redirect()->route('actualites.index')->with('success', 'Actualité créée avec succès.');
    }

    /**
     * Affiche les détails d'une actualité.
     */
    public function show(Actualite $actualite)
    {
        return view('actualites.show', compact('actualite'));
    }

    /**
     * Affiche le formulaire d'édition pour une actualité.
     */
    public function edit(Actualite $actualite)
    {
        return view('actualites.edit', compact('actualite'));
    }

    /**
     * Met à jour une actualité existante.
     */
    public function update(Request $request, Actualite $actualite)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'date_publication' => 'required|date',
            'date_expiration' => 'nullable|date|after:date_publication',
            'tags' => 'nullable|string',
        ]);

        $actualite->update($request->all());

        return redirect()->route('actualites.index')->with('success', 'Actualité mise à jour avec succès.');
    }

    /**
     * Supprime une actualité.
     */
    public function destroy(Actualite $actualite)
    {
        $actualite->delete();

        return redirect()->route('actualites.index')->with('success', 'Actualité supprimée avec succès.');
    }
}

