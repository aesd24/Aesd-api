<?php

namespace App\Http\Controllers;

use App\Models\Programme;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgrammeController extends Controller
{
    // Afficher la liste des programmes
    public function index()
    {
        $programmes = Programme::with('church')->get(); // Récupérer tous les programmes avec leurs églises
        return view('programmes.index', compact('programmes')); // Retourner la vue avec les programmes
    }

    // Afficher le formulaire de création d'un programme
    public function create()
    {
        $churches = Church::all(); // Récupérer toutes les églises pour le select
        return view('programmes.create', compact('churches')); // Retourner la vue de création
    }

    // Stocker un nouveau programme
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Validation du fichier
            'church_id' => 'required|exists:churches,id', // Assurez-vous que l'église existe
        ]);

        // Gérer le fichier si présent
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('files/programmes', 'public'); // Enregistrer le fichier
        }

        // Créer un nouveau programme
        $programme = Programme::create([
            'description' => $request->description,
            'file' => $filePath,
            'church_id' => $request->church_id,
        ]);

        return redirect()->route('programmes.index')->with('success', 'Programme créé avec succès.');
    }

    // Afficher un programme spécifique
    public function show(Programme $programme)
    {
        return view('programmes.show', compact('programme')); // Retourner la vue pour afficher le programme
    }

    // Afficher le formulaire d'édition d'un programme
    public function edit(Programme $programme)
    {
        $churches = Church::all(); // Récupérer toutes les églises pour le select
        return view('programmes.edit', compact('programme', 'churches')); // Retourner la vue d'édition
    }

    // Mettre à jour un programme
    public function update(Request $request, Programme $programme)
    {
        // Valider les données
        $request->validate([
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Validation du fichier
            'church_id' => 'required|exists:churches,id', // Assurez-vous que l'église existe
        ]);

        // Gérer le fichier si présent
        $filePath = $programme->file; // Garder l'ancien fichier par défaut
        if ($request->hasFile('file')) {
            // Supprimer l'ancien fichier si existe
            if ($programme->file) {
                Storage::disk('public')->delete($programme->file);
            }
            // Enregistrer le nouveau fichier
            $filePath = $request->file('file')->store('files/programmes', 'public');
        }

        // Mettre à jour le programme
        $programme->update([
            'description' => $request->description,
            'file' => $filePath,
            'church_id' => $request->church_id,
        ]);

        return redirect()->route('programmes.index')->with('success', 'Programme mis à jour avec succès.');
    }

    // Supprimer un programme
    public function destroy(Programme $programme)
    {
        // Supprimer le fichier du disque si existe
        if ($programme->file) {
            Storage::disk('public')->delete($programme->file);
        }

        $programme->delete(); // Supprimer le programme

        return redirect()->route('programmes.index')->with('success', 'Programme supprimé avec succès.');
    }
}
