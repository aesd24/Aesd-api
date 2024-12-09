<?php

namespace App\Http\Controllers;

use App\Models\Ceremonie;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiteurDeDieu;

class CeremonieController extends Controller
{


    public function index()
    {
        $user = auth()->user();

        // Vérifie que l'utilisateur a un ServiteurDeDieu associé
        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();
        if (!$serviteur) {
            return redirect()->back()->with('error', 'Aucun serviteur trouvé pour cet utilisateur.');
        }

        // Récupère les cérémonies liées aux églises du serviteur
        $ceremonies = Ceremonie::whereHas('churches', function ($query) use ($serviteur) {
            // On cherche les églises associées à ce serviteur
            $query->where('owner_servant_id', $serviteur->id);
        })
            // On charge la relation 'churches' pour les utiliser dans la vue
            ->with('churches')
            ->get();

        // Retourner la vue avec les cérémonies récupérées
        return view('ceremonies.index', compact('ceremonies'));
    }





    public function create()
    {
        // Utilisateur actuellement connecté
        $user = auth()->user();

        // Récupérer le ServiteurDeDieu correspondant à l'utilisateur
        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Vérifier si un ServiteurDeDieu est trouvé
        if (!$serviteur) {
            return redirect()->back()->with('error', 'Aucun serviteur associé à cet utilisateur.');
        }

        // Récupérer les églises créées par le serviteur (utilisateur connecté)
        $churches = Church::where('owner_servant_id', $serviteur->id)->get();

        // Vérifier si des églises sont trouvées
        if ($churches->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune église trouvée pour ce serviteur.');
        }

        // Retourner la vue de création de cérémonie avec les églises disponibles
        return view('ceremonies.create', compact('churches'));
    }





    /**
     * Crée une nouvelle cérémonie et l'associe à des églises.
     */
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480',
            'event_date' => 'required|date',
            'churches' => 'required|array',
            'churches.*' => 'exists:churches,id',
            'periode_time' => 'required|date',
        ], [
            'title.required' => 'Le titre de la cérémonie est obligatoire.',
            'title.string' => 'Le titre doit être une chaîne de caractères valide.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères valide.',
            'media.file' => 'Le fichier média doit être une image ou une vidéo.',
            'media.mimes' => 'Le fichier média doit avoir une extension valide (jpeg, png, jpg, gif, mp4, mov, avi).',
            'media.max' => 'Le fichier média ne peut pas dépasser 20 Mo.',
            'event_date.required' => 'La date de l\'événement est obligatoire.',
            'event_date.date' => 'La date de l\'événement doit être une date valide.',
            'churches.required' => 'Vous devez sélectionner au moins une église.',
            'churches.array' => 'Les églises doivent être fournies sous forme de tableau.',
            'churches.*.exists' => 'L\'église sélectionnée n\'existe pas.',
            'periode_time.required' => 'La période est obligatoire.',
            'periode_time.date' => 'La période doit être une date valide.',
        ]);

        // Gestion du fichier média s'il existe
        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaFile = $request->file('media');
            $mediaPath = $mediaFile->store('media', 'public');
        }

        // Création de la cérémonie
        $ceremonie = Ceremonie::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'] ?? null,
            'media' => $mediaPath,
            'event_date' => $validatedData['event_date'],
        ]);

        // Association des églises avec la période
        $syncData = [];
        foreach ($request->churches as $church_id) {
            $syncData[$church_id] = ['periode_time' => $validatedData['periode_time']];
        }
        $ceremonie->churches()->sync($syncData);

        // Réponse JSON
        return response()->json([
            'success' => true,
            'message' => 'Cérémonie créée avec succès.',
            'data' => $ceremonie->load('churches'),
        ], 201);
    }



    public function show($id)
    {
        // Récupérer la cérémonie par ID
        $ceremonie = Ceremonie::findOrFail($id);

        // Passer l'objet cérémonie à la vue
        return view('ceremonies.show', compact('ceremonie'));
    }





    // Méthode pour afficher le formulaire de modification d'une cérémonie
    public function edit($id)
    {
        // Récupérer la cérémonie par son ID
        $ceremonie = Ceremonie::findOrFail($id);

        // Récupérer toutes les églises disponibles
        $churches = Church::all();

        // Retourner la vue avec les données
        return view('ceremonies.edit', compact('ceremonie', 'churches'));
    }



    // Méthode pour mettre à jour la cérémonie
    public function update(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'media' => 'nullable|mimes:jpg,jpeg,png,mp4,avi,mov|max:2048',
            'id_eglise' => 'required|exists:churches,id',
        ]);

        // Récupérer la cérémonie à mettre à jour
        $ceremonie = Ceremonie::findOrFail($id);

        // Mise à jour des informations de la cérémonie
        $ceremonie->title = $request->input('title');
        $ceremonie->description = $request->input('description');
        $ceremonie->event_date = $request->input('event_date');

        // Gérer le fichier média (si fourni)
        if ($request->hasFile('media')) {
            // Vérifier si un média existe déjà et le supprimer
            if ($ceremonie->media && Storage::disk('public')->exists($ceremonie->media)) {
                Storage::disk('public')->delete($ceremonie->media);
            }

            // Enregistrer le nouveau fichier média
            $mediaPath = $request->file('media')->store('media', 'public');
            $ceremonie->media = $mediaPath;
        }

        // Associer l'église sélectionnée (pour une relation BelongsTo)
        $ceremonie->id_eglise = $request->input('id_eglise'); // Affecter directement l'ID de l'église

        // Sauvegarder les changements
        $ceremonie->save();

        // Redirection après la mise à jour
        return redirect()->route('ceremonies.index')->with('success', 'Cérémonie mise à jour avec succès!');
    }





    // public function destroy($id)
    // {
    //     $ceremonie = Ceremonie::findOrFail($id);
    //     $ceremonie->delete();

    //     return redirect()->route('ceremonies.index')->with('success', 'Cérémonie supprimée avec succès.');
    // }


    public function destroy($id)
    {
        // Récupérer la cérémonie par son ID
        $ceremonie = Ceremonie::findOrFail($id);

        // Vérifier si une image/média est associé(e) et la supprimer du stockage
        if ($ceremonie->media && Storage::disk('public')->exists($ceremonie->media)) {
            Storage::disk('public')->delete($ceremonie->media);
        }

        // Supprimer la cérémonie de la base de données
        $ceremonie->delete();

        // Redirection avec un message de succès
        return redirect()->route('ceremonies.index')->with('success', 'Cérémonie et son média supprimés avec succès.');
    }
}
