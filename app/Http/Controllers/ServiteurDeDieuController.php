<?php

namespace App\Http\Controllers;

use App\Models\ServiteurDeDieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiteurDeDieuController extends Controller
{
    // Afficher la liste des serviteurs de Dieu
    public function index()
    {
        $serviteurs = ServiteurDeDieu::with(['user', 'church'])->get(); // Récupérer tous les serviteurs avec leurs utilisateurs et églises
        return view('serviteurs.index', compact('serviteurs')); // Retourner la vue avec les serviteurs
    }

    // Afficher le formulaire de création d'un serviteur de Dieu
    public function create()
    {
        return view('serviteurs.create'); // Retourner la vue de création
    }

    // Stocker un nouveau serviteur de Dieu
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'is_main' => 'required|boolean',
            'id_card_recto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card_verso' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id' => 'required|exists:users,id',
            'church_id' => 'required|exists:churches,id',
        ]);

        // Créer un nouveau serviteur de Dieu
        $serviteur = ServiteurDeDieu::create($request->all());

        // Gérer le téléchargement des images
        if ($request->hasFile('id_card_recto')) {
            $serviteur->id_card_recto = $request->file('id_card_recto')->store('id_cards', 'public');
        }

        if ($request->hasFile('id_card_verso')) {
            $serviteur->id_card_verso = $request->file('id_card_verso')->store('id_cards', 'public');
        }

        $serviteur->save();

        return redirect()->route('serviteurs.index')->with('success', 'Serviteur de Dieu créé avec succès.');
    }

    // Afficher un serviteur de Dieu spécifique
    public function show(ServiteurDeDieu $serviteur)
    {
        return view('serviteurs.show', compact('serviteur')); // Retourner la vue pour afficher le serviteur
    }

    // Afficher le formulaire d'édition d'un serviteur de Dieu
    public function edit(ServiteurDeDieu $serviteur)
    {
        return view('serviteurs.edit', compact('serviteur')); // Retourner la vue d'édition
    }

    // Mettre à jour un serviteur de Dieu
    public function update(Request $request, ServiteurDeDieu $serviteur)
    {
        // Valider les données
        $request->validate([
            'is_main' => 'required|boolean',
            'user_id' => 'required|exists:users,id',
            'church_id' => 'required|exists:churches,id',
        ]);

        // Mettre à jour le serviteur de Dieu
        $serviteur->update($request->all());

        // Gérer le téléchargement des images
        if ($request->hasFile('id_card_recto')) {
            // Supprimer l'ancienne image si elle existe
            if ($serviteur->id_card_recto) {
                Storage::disk('public')->delete($serviteur->id_card_recto);
            }
            $serviteur->id_card_recto = $request->file('id_card_recto')->store('id_cards', 'public');
        }

        if ($request->hasFile('id_card_verso')) {
            // Supprimer l'ancienne image si elle existe
            if ($serviteur->id_card_verso) {
                Storage::disk('public')->delete($serviteur->id_card_verso);
            }
            $serviteur->id_card_verso = $request->file('id_card_verso')->store('id_cards', 'public');
        }

        $serviteur->save();

        return redirect()->route('serviteurs.index')->with('success', 'Serviteur de Dieu mis à jour avec succès.');
    }

    // Supprimer un serviteur de Dieu
    public function destroy(ServiteurDeDieu $serviteur)
    {
        // Supprimer les images si elles existent
        if ($serviteur->id_card_recto) {
            Storage::disk('public')->delete($serviteur->id_card_recto);
        }

        if ($serviteur->id_card_verso) {
            Storage::disk('public')->delete($serviteur->id_card_verso);
        }

        $serviteur->delete(); // Supprimer le serviteur

        return redirect()->route('serviteurs.index')->with('success', 'Serviteur de Dieu supprimé avec succès.');
    }
}
