<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ceremonie;
use App\Models\Church;
use App\Models\ServiteurDeDieu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CeremonieApiController extends Controller
{
    // Retourne toutes les cérémonies avec les églises associées
    /**
     * Liste les cérémonies associées aux églises du Serviteur de Dieu.
     */
    public function index()
    {
        $user = Auth::user();

        // Vérifie que l'utilisateur a un ServiteurDeDieu associé
        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();
        if (!$serviteur) {
            return response()->json(['error' => 'Aucun serviteur trouvé pour cet utilisateur.'], 403);
        }

        // Récupère les cérémonies liées aux églises du serviteur
        $ceremonies = Ceremonie::whereHas('churches', function ($query) use ($serviteur) {
            // On cherche les églises associées à ce serviteur
            $query->where('owner_servant_id', $serviteur->id);
        })
            // Charger la relation 'churches'
            ->with('churches')
            ->get();

        // Retourne les cérémonies avec leurs églises en réponse JSON
        return response()->json([
            'success' => true,
            'ceremonies' => $ceremonies,
        ]);
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



    /**
     * Récupère les églises associées au ServiteurDeDieu connecté pour créer une cérémonie.
     */
    public function getChurchesForCeremony()
    {
        // Utilisateur actuellement connecté
        $user = auth()->user();

        // Récupérer le ServiteurDeDieu correspondant à l'utilisateur
        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Vérifier si un ServiteurDeDieu est trouvé
        if (!$serviteur) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun serviteur associé à cet utilisateur.',
            ], 404);
        }

        // Récupérer les églises créées par le serviteur (utilisateur connecté)
        $churches = Church::where('owner_servant_id', $serviteur->id)->get();

        // Vérifier si des églises sont trouvées
        if ($churches->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune église trouvée pour ce serviteur.',
            ], 404);
        }

        // Réponse avec les églises disponibles
        return response()->json([
            'success' => true,
            'message' => 'Églises récupérées avec succès.',
            'data' => $churches,
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




    /**
     * Récupérer les détails d'une cérémonie pour modification.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        // Récupérer la cérémonie par son ID ou renvoyer une erreur 404
        $ceremonie = Ceremonie::find($id);

        if (!$ceremonie) {
            return response()->json([
                'success' => false,
                'message' => 'Cérémonie non trouvée.',
            ], 404);
        }

        // Récupérer toutes les églises disponibles
        $churches = Church::all();

        // Retourner les données dans une réponse JSON
        return response()->json([
            'success' => true,
            'message' => 'Détails de la cérémonie récupérés avec succès.',
            'data' => [
                'ceremonie' => $ceremonie,
                'churches' => $churches,
            ],
        ], 200);
    }

    /**
     * Mettre à jour une cérémonie.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validation des données
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'media' => 'nullable|mimes:jpg,jpeg,png,mp4,avi,mov|max:2048',
            'id_eglise' => 'required|exists:churches,id',
        ]);

        // Récupérer la cérémonie à mettre à jour ou renvoyer une erreur
        $ceremonie = Ceremonie::find($id);

        if (!$ceremonie) {
            return response()->json([
                'success' => false,
                'message' => 'Cérémonie non trouvée.',
            ], 404);
        }

        // Mise à jour des informations
        $ceremonie->title = $validatedData['title'];
        $ceremonie->description = $validatedData['description'];
        $ceremonie->event_date = $validatedData['event_date'];

        // Gérer le fichier média (si fourni)
        if ($request->hasFile('media')) {
            // Supprimer l'ancien média s'il existe
            if ($ceremonie->media && Storage::disk('public')->exists($ceremonie->media)) {
                Storage::disk('public')->delete($ceremonie->media);
            }

            // Enregistrer le nouveau fichier média
            $mediaPath = $request->file('media')->store('media', 'public');
            $ceremonie->media = $mediaPath;
        }

        // Associer l'église sélectionnée
        $ceremonie->id_eglise = $validatedData['id_eglise'];

        // Sauvegarder les changements
        $ceremonie->save();

        // Réponse JSON après mise à jour
        return response()->json([
            'success' => true,
            'message' => 'Cérémonie mise à jour avec succès.',
            'data' => $ceremonie,
        ], 200);
    }



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

        // Retourner une réponse JSON pour indiquer le succès
        return response()->json([
            'success' => true,
            'message' => 'Cérémonie et son média supprimés avec succès.'
        ], 200);
    }
}
