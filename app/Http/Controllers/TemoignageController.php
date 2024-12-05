<?php

namespace App\Http\Controllers;

use App\Models\Temoignage;
use Illuminate\Http\Request;

class TemoignageController extends Controller
{
    // Afficher la liste des témoignages
    public function index()
    {
        $temoignages = Temoignage::with('user')->get(); // Récupérer tous les témoignages avec les utilisateurs
        return view('temoignages.index', compact('temoignages')); // Retourner la vue avec les témoignages
    }

    // Afficher le formulaire de création d'un témoignage
    public function create()
    {
        return view('temoignages.create'); // Retourner la vue de création
    }

    // Stocker un nouveau témoignage
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'is_anonymous' => 'boolean', // Optionnel
            'user_id' => 'required|exists:users,id', // Assurez-vous que l'utilisateur existe
        ]);

        // Créer un nouveau témoignage
        Temoignage::create($request->all());

        return redirect()->route('temoignages.index')->with('success', 'Témoignage créé avec succès.');
    }

    // Afficher un témoignage spécifique
    public function show(Temoignage $temoignage)
    {
        return view('temoignages.show', compact('temoignage')); // Retourner la vue pour afficher le témoignage
    }

    // Afficher le formulaire d'édition d'un témoignage
    public function edit(Temoignage $temoignage)
    {
        return view('temoignages.edit', compact('temoignage')); // Retourner la vue d'édition
    }

    // Mettre à jour un témoignage
    public function update(Request $request, Temoignage $temoignage)
    {
        // Valider les données
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'is_anonymous' => 'boolean', // Optionnel
        ]);

        // Mettre à jour le témoignage
        $temoignage->update($request->all());

        return redirect()->route('temoignages.index')->with('success', 'Témoignage mis à jour avec succès.');
    }

    // Supprimer un témoignage
    public function destroy(Temoignage $temoignage)
    {
        $temoignage->delete(); // Supprimer le témoignage

        return redirect()->route('temoignages.index')->with('success', 'Témoignage supprimé avec succès.');
    }
}
