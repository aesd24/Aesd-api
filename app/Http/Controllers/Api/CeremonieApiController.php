<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ceremonie;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CeremonieApiController extends Controller
{
    // Retourne toutes les cérémonies avec les églises associées
    public function index()
    {
        $ceremonies = Ceremonie::with('churches')->get();
        return response()->json([
            'success' => true,
            'data' => $ceremonies,
        ], 200);
    }

    // Retourne une cérémonie spécifique avec les églises associées
    public function show($id)
    {
        $ceremonie = Ceremonie::with('churches')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $ceremonie,
        ], 200);
    }

    // Créer une nouvelle cérémonie
    public function store(Request $request)
    {
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
            'media.mimes' => 'Le fichier média doit avoir l\'une des extensions suivantes : jpeg, png, jpg, gif, mp4, mov, avi.',
            'media.max' => 'Le fichier média ne peut pas dépasser 20 Mo.',
            'event_date.required' => 'La date de l\'événement est obligatoire.',
            'event_date.date' => 'La date de l\'événement doit être une date valide.',
            'churches.required' => 'Vous devez sélectionner au moins une église.',
            'churches.array' => 'Les églises doivent être sous forme de tableau.',
            'churches.*.exists' => 'L\'église sélectionnée n\'existe pas.',
            'periode_time.required' => 'La période est obligatoire.',
            'periode_time.date' => 'La période doit être une date valide.',
        ]);

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaFile = $request->file('media');
            $mediaPath = $mediaFile->store('media', 'public');
        }

        // Créer la cérémonie avec les données validées
        $ceremonie = Ceremonie::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'media' => $mediaPath,
            'event_date' => $validatedData['event_date'],
        ]);

        // Gérer l'association avec les églises et la période
        $syncData = [];
        foreach ($request->churches as $church_id) {
            $syncData[$church_id] = ['periode_time' => $request->periode_time];
        }
        $ceremonie->churches()->sync($syncData);

        return response()->json([
            'success' => true,
            'message' => 'Cérémonie créée avec succès.',
            'data' => $ceremonie,
        ], 201);
    }

    // Mettre à jour une cérémonie existante
    public function update(Request $request, $id)
    {
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
            'media.mimes' => 'Le fichier média doit avoir l\'une des extensions suivantes : jpeg, png, jpg, gif, mp4, mov, avi.',
            'media.max' => 'Le fichier média ne peut pas dépasser 20 Mo.',
            'event_date.required' => 'La date de l\'événement est obligatoire.',
            'event_date.date' => 'La date de l\'événement doit être une date valide.',
            'churches.required' => 'Vous devez sélectionner au moins une église.',
            'churches.array' => 'Les églises doivent être sous forme de tableau.',
            'churches.*.exists' => 'L\'église sélectionnée n\'existe pas.',
            'periode_time.required' => 'La période est obligatoire.',
            'periode_time.date' => 'La période doit être une date valide.',
        ]);

        $ceremonie = Ceremonie::findOrFail($id);
        $mediaPath = $ceremonie->media;
        if ($request->hasFile('media')) {
            // Supprimer l'ancien fichier média s'il existe
            if ($ceremonie->media) {
                Storage::disk('public')->delete($ceremonie->media);
            }
            // Stocker le nouveau fichier
            $mediaFile = $request->file('media');
            $mediaPath = $mediaFile->store('media', 'public');
        }

        $ceremonie->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'media' => $mediaPath,
            'event_date' => $validatedData['event_date'],
        ]);

        $syncData = [];
        foreach ($request->churches as $church_id) {
            $syncData[$church_id] = ['periode_time' => $validatedData['periode_time']];
        }
        $ceremonie->churches()->sync($syncData);

        return response()->json([
            'success' => true,
            'message' => 'Cérémonie mise à jour avec succès.',
            'data' => $ceremonie,
        ], 200);
    }

    // Supprimer une cérémonie existante
    public function destroy($id)
    {
        $ceremonie = Ceremonie::findOrFail($id);
        // Supprimer le fichier média associé à la cérémonie si nécessaire
        if ($ceremonie->media) {
            Storage::disk('public')->delete($ceremonie->media);
        }
        $ceremonie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cérémonie supprimée avec succès.',
        ], 200);
    }
}
