<?php

namespace App\Http\Controllers;

use App\Models\Don;
use Illuminate\Http\Request;

class DonController extends Controller
{
    // Afficher la liste des dons
    public function all_index()
    {
        $dons = Don::all(); // Récupérer tous les dons
        return view('dons.index', compact('dons')); // Retourner la vue avec les dons
    }




    public function index()
    {
        // Récupérer les dons de l'utilisateur connecté
        $dons = auth()->user()->dons; // Accéder aux dons associés à l'utilisateur connecté via la relation 'dons'


        // dd($dons);

        return view('dons.index', compact('dons'));
    }


    // Afficher le formulaire de création d'un don
    public function create()
    {
        return view('dons.create'); // Retourner la vue de création
    }

    // Stocker un nouveau don
    // public function store(Request $request)
    // {

    //     dd($request);
    //     // Valider les données
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'objectif' => 'required|numeric',
    //         'end_at' => 'required|date',
    //         'status' => 'required|string',
    //     ]);

    //     // Créer un nouveau don
    //     Don::create($request->all());

    //     return redirect()->route('dons.index')->with('success', 'Don créé avec succès.');
    // }






    public function store(Request $request)
    {
        // Valider les données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectif' => 'required|numeric',
            // 'end_at' => 'required|date',
            // 'status' => 'required|string',
        ]);

        // Créer un nouveau don avec les données validées
        $don = Don::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'objectif' => $validated['objectif'],
            // 'end_at' => $validated['end_at'],
            // 'status' => $validated['status'],

            'end_at' => now(),
            // 'status' => 'Effectuer',
            'status' => 1,
        ]);

        // Optionnel: Associer le don à l'utilisateur authentifié (si nécessaire)
        if ($request->user()) {
            $don->users()->attach($request->user()->id, [
                'reference_paiement' => 'Référence001', // Exemple
                'date_paiement' => now(),
                'montant_paiement' => 100.00,  // Exemple de montant
            ]);
        }

        // Retourner une réponse, rediriger avec un message de succès
        return redirect()->route('dons.index')->with('success', 'Don créé avec succès.');
    }

    // Afficher un don spécifique
    public function show(Don $don)
    {
        return view('dons.show', compact('don')); // Retourner la vue pour afficher le don
    }

    // Afficher le formulaire d'édition d'un don
    public function edit(Don $don)
    {
        return view('dons.edit', compact('don')); // Retourner la vue d'édition
    }

    // Mettre à jour un don
    public function update(Request $request, Don $don)
    {
        // Valider les données
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectif' => 'required|numeric',
            'end_at' => 'required|date',
            'status' => 'required|string',
        ]);

        // Mettre à jour le don
        $don->update($request->all());

        return redirect()->route('dons.index')->with('success', 'Don mis à jour avec succès.');
    }

    // Supprimer un don
    public function destroy(Don $don)
    {
        $don->delete(); // Supprimer le don

        return redirect()->route('dons.index')->with('success', 'Don supprimé avec succès.');
    }
}
