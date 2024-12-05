<?php

namespace App\Http\Controllers;

use App\Models\Fidele;
use Illuminate\Http\Request;

class FideleController extends Controller
{
    // Afficher la liste des fidèles
    public function index()
    {
        $fideles = Fidele::all(); // Récupérer tous les fidèles
        return view('fideles.index', compact('fideles')); // Retourner la vue avec les fidèles
    }

    // Afficher le formulaire de création d'un fidèle
    public function create()
    {
        return view('fideles.create'); // Retourner la vue de création
    }

    // Stocker un nouveau fidèle
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'user_id' => 'required|exists:users,id', // Vérifier que l'utilisateur existe
            'church_id' => 'required|exists:churches,id', // Vérifier que l'église existe
        ]);

        // Créer un nouveau fidèle
        Fidele::create($request->all());

        return redirect()->route('fideles.index')->with('success', 'Fidèle créé avec succès.');
    }

    // Afficher un fidèle spécifique
    public function show(Fidele $fidele)
    {
        return view('fideles.show', compact('fidele')); // Retourner la vue pour afficher le fidèle
    }

    // Afficher le formulaire d'édition d'un fidèle
    public function edit(Fidele $fidele)
    {
        return view('fideles.edit', compact('fidele')); // Retourner la vue d'édition
    }

    // Mettre à jour un fidèle
    public function update(Request $request, Fidele $fidele)
    {
        // Valider les données
        $request->validate([
            'user_id' => 'required|exists:users,id', // Vérifier que l'utilisateur existe
            'church_id' => 'required|exists:churches,id', // Vérifier que l'église existe
        ]);

        // Mettre à jour le fidèle
        $fidele->update($request->all());

        return redirect()->route('fideles.index')->with('success', 'Fidèle mis à jour avec succès.');
    }

    // Supprimer un fidèle
    public function destroy(Fidele $fidele)
    {
        $fidele->delete(); // Supprimer le fidèle

        return redirect()->route('fideles.index')->with('success', 'Fidèle supprimé avec succès.');
    }
}
