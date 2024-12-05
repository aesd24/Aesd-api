<?php

namespace App\Http\Controllers;

use App\Models\Administrateur;
use Illuminate\Http\Request;

class AdministrateurController extends Controller
{
    /**
     * Afficher la liste des administrateurs.
     */
    public function index()
    {
        $administrateurs = Administrateur::all();
        return view('administrateurs.index', compact('administrateurs'));
    }

    /**
     * Afficher le formulaire de création d'un nouvel administrateur.
     */
    public function create()
    {
        return view('administrateurs.create');
    }

    /**
     * Enregistrer un nouvel administrateur.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_card' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        Administrateur::create($validatedData);

        return redirect()->route('administrateurs.index')->with('success', 'Administrateur créé avec succès.');
    }

    /**
     * Afficher les détails d'un administrateur.
     */
    public function show(Administrateur $administrateur)
    {
        return view('administrateurs.show', compact('administrateur'));
    }

    /**
     * Afficher le formulaire d'édition d'un administrateur.
     */
    public function edit(Administrateur $administrateur)
    {
        return view('administrateurs.edit', compact('administrateur'));
    }

    /**
     * Mettre à jour les informations d'un administrateur.
     */
    public function update(Request $request, Administrateur $administrateur)
    {
        $validatedData = $request->validate([
            'id_card' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $administrateur->update($validatedData);

        return redirect()->route('administrateurs.index')->with('success', 'Administrateur mis à jour avec succès.');
    }

    /**
     * Supprimer un administrateur.
     */
    public function destroy(Administrateur $administrateur)
    {
        $administrateur->delete();

        return redirect()->route('administrateurs.index')->with('success', 'Administrateur supprimé avec succès.');
    }
}
