<?php

namespace App\Http\Controllers;

use App\Models\OpportuniteJeune;
use Illuminate\Http\Request;

class OpportuniteJeuneController extends Controller
{
    // Afficher la liste des opportunités
    public function index()
    {
        $opportunites = OpportuniteJeune::all(); // Récupérer toutes les opportunités
        return view('opportunites.index', compact('opportunites')); // Retourner la vue avec les opportunités
    }

    // Afficher le formulaire de création d'une opportunité
    public function create()
    {
        return view('opportunites.create'); // Retourner la vue de création
    }

    // Stocker une nouvelle opportunité
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'post_profile' => 'required|string',
            'exigence' => 'required|string',
            'deadline' => 'required|date',
            'localisation_du_poste' => 'required|string',
            'is_published_at' => 'boolean',
            'study_level' => 'string|nullable',
            'experience' => 'string|nullable',
            'type_contract' => 'string|nullable',
        ]);

        // Créer une nouvelle opportunité
        OpportuniteJeune::create($request->all());

        return redirect()->route('opportunites.index')->with('success', 'Opportunité créée avec succès.');
    }

    // Afficher une opportunité spécifique
    public function show(OpportuniteJeune $opportunite)
    {
        return view('opportunites.show', compact('opportunite')); // Retourner la vue pour afficher l'opportunité
    }

    // Afficher le formulaire d'édition d'une opportunité
    public function edit(OpportuniteJeune $opportunite)
    {
        return view('opportunites.edit', compact('opportunite')); // Retourner la vue d'édition
    }

    // Mettre à jour une opportunité
    public function update(Request $request, OpportuniteJeune $opportunite)
    {
        // Valider les données
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'post_profile' => 'required|string',
            'exigence' => 'required|string',
            'deadline' => 'required|date',
            'localisation_du_poste' => 'required|string',
            'is_published_at' => 'boolean',
            'study_level' => 'string|nullable',
            'experience' => 'string|nullable',
            'type_contract' => 'string|nullable',
        ]);

        // Mettre à jour l'opportunité
        $opportunite->update($request->all());

        return redirect()->route('opportunites.index')->with('success', 'Opportunité mise à jour avec succès.');
    }

    // Supprimer une opportunité
    public function destroy(OpportuniteJeune $opportunite)
    {
        $opportunite->delete(); // Supprimer l'opportunité

        return redirect()->route('opportunites.index')->with('success', 'Opportunité supprimée avec succès.');
    }
}
