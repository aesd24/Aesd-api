<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChurchRequest;
use App\Models\Church;
use App\Models\ServiteurDeDieu;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chantre;
use App\Models\Fidele;
use Illuminate\Support\Facades\Auth;

class ChurchApiController extends Controller
{

    public function index()
    {
        // Obtenir l'utilisateur actuellement connecté
        $user = auth()->user();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }

        // Récupérer le ServiteurDeDieu correspondant à l'utilisateur
        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Vérifier si un serviteur a été trouvé
        if (!$serviteur) {
            return response()->json([
                'message' => 'Aucun serviteur trouvé pour cet utilisateur.'
            ], 404);
        }

        // Récupérer les églises créées par le serviteur
        $churches = Church::where('owner_servant_id', $serviteur->id)->get();

        // Retourner les églises sous forme de JSON
        return response()->json([
            'message' => 'Églises récupérées avec succès.',
            'data' => $churches,
        ], 200);
    }


    /**
     * Afficher les détails d'une église.
     */
    public function show(Church $church): JsonResponse
    {
        return $this->successResponse($church);
    }



    public function store(Request $request)
    {
        // Validation des données d'entrée
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:15',
            'adresse' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation du logo
            'is_main' => 'required|boolean',
            'description' => 'nullable|string',
            'type_church' => 'nullable|string',
            'attestation_file_path' => 'nullable|file|mimes:pdf|max:2048|required_if:is_main,true', // Requis si is_main est vrai
        ], [
            // Messages personnalisés
            'name.required' => 'Le champ nom est obligatoire.',
            'name.string' => 'Le champ nom doit être une chaîne de caractères.',
            'name.max' => 'Le champ nom ne doit pas dépasser :max caractères.',
            'email.email' => 'L\'adresse email doit être valide.',
            'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être de type :values.',
            'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',
            'is_main.required' => 'Le champ "principal" est requis.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',
            'attestation_file_path.file' => 'Le fichier d\'attestation doit être un fichier valide.',
            'attestation_file_path.mimes' => 'L\'attestation doit être un fichier PDF.',
            'attestation_file_path.max' => 'L\'attestation ne doit pas dépasser :max Ko.',
            'attestation_file_path.required_if' => 'L\'attestation est requise lorsque l\'église est principale.',
        ]);

        try {
            // Vérifier si l'église principale existe déjà pour l'utilisateur connecté
            if ($validatedData['is_main']) {
                $churchIdOwnedByUser = Church::where('owner_servant_id', auth()->id())->value('id');
                if ($churchIdOwnedByUser) {
                    return response()->json(['error' => 'Vous avez déjà une église principale.'], 400);
                }
            }

            // Gestion du fichier logo
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                $logoFilename = $validatedData['name'] . '_' . time() . '.' . $logoFile->getClientOriginalExtension();
                $logoPath = $logoFile->storeAs('public/logos', $logoFilename);
            }

            // Gestion du fichier d'attestation
            $attestationPath = null;
            if ($validatedData['is_main'] && $request->hasFile('attestation_file_path')) {
                $attestationFile = $request->file('attestation_file_path');
                $attestationFilename = $validatedData['name'] . '_attestation_' . time() . '.' . $attestationFile->getClientOriginalExtension();
                $attestationPath = $attestationFile->storeAs('public/attestations', $attestationFilename);
            }

            // Récupérer le serviteur de Dieu connecté
            $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();
            if (!$serviteurDeDieu) {
                return response()->json(['error' => 'Aucun serviteur trouvé pour l\'utilisateur connecté.'], 404);
            }

            // Créer l'église
            $newChurch = Church::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'] ?? null,
                'phone' => $validatedData['phone'] ?? null,
                'adresse' => $validatedData['adresse'] ?? null,
                'logo' => $logoPath ?? null,
                'is_main' => $validatedData['is_main'],
                'description' => $validatedData['description'] ?? null,
                'owner_servant_id' => $serviteurDeDieu->id,
                'type_church' => $validatedData['type_church'] ?? null,
                'attestation_file_path' => $attestationPath ?? null,
            ]);

            // Si l'église est principale, mettre à jour les informations du serviteur
            if ($validatedData['is_main']) {
                $newChurch->update([
                    'main_church_id' => $newChurch->id,
                ]);

                $serviteurDeDieu->update([
                    'church_id' => $newChurch->id,
                    'is_assigned' => 1,
                    'is_main' => 1,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Église créée avec succès.',
                'church' => $newChurch
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la création de l\'église.',
                'message' => $e->getMessage()
            ], 500);
        }
    }





   



    /**
     * Permet à un utilisateur de choisir une église en fonction de son rôle (Fidèle, Chantre, Serviteur de Dieu).
     */
    public function choisirEglise()
    {
        $user = Auth::user(); // Récupère l'utilisateur actuellement connecté

        // Récupère toutes les églises
        $eglises = Church::all();

        // Vérification des rôles de l'utilisateur : Fidèle, Chantre ou Serviteur de Dieu
        $fidele = Fidele::where('user_id', $user->id)->first();
        $chantre = Chantre::where('user_id', $user->id)->first();
        $serviteur_de_dieu = ServiteurDeDieu::where('user_id', $user->id)->first();

        $selectedChurchId = null; // Initialisation de l'église sélectionnée

        if ($fidele) {
            // Si l'utilisateur est un Fidèle, récupérer l'église associée
            $selectedChurchId = $fidele->church_id;
        } elseif ($chantre) {
            // Si l'utilisateur est un Chantre, récupérer l'église associée
            $selectedChurchId = $chantre->church_id;
        } elseif ($serviteur_de_dieu) {
            // Si l'utilisateur est un Serviteur de Dieu
            if ($serviteur_de_dieu->is_main) {
                return response()->json(['error' => 'Votre statut actuel ne vous permet pas de choisir une église.'], 403);
            }
            // Si ce n'est pas un Serviteur Principal, récupérer l'église associée
            $selectedChurchId = $serviteur_de_dieu->church_id;
        } else {
            // Si l'utilisateur n'a pas de rôle valide, renvoyer une erreur
            return response()->json(['error' => 'Vous devez être un Fidèle, un Chantre ou un Serviteur de Dieu pour choisir une église.'], 403);
        }

        return response()->json([
            'eglises' => $eglises,
            'selected_church_id' => $selectedChurchId,
        ]);
    }


   /**
     * Sauvegarde l'église sélectionnée par l'utilisateur.
     */
    public function sauvegarderEgliseSelectionnee(Request $request)
    {
        // Validation des données reçues
        $request->validate([
            'church_id' => ['required', 'exists:churches,id'], // Valide que l'ID existe dans la table des églises
        ], [
            'church_id.required' => 'Veuillez sélectionner une église.',
            'church_id.exists' => 'L\'église sélectionnée est invalide.',
        ]);

        $user = Auth::user(); // Récupère l'utilisateur connecté
        $churchId = $request->church_id; // Récupère l'ID de l'église sélectionnée

        // Vérification si l'utilisateur est un Fidele
        $fidele = Fidele::where('user_id', $user->id)->first();

        // Vérification si l'utilisateur est un Chantre
        $chantre = Chantre::where('user_id', $user->id)->first();

        // Vérification si l'utilisateur est un serviteur de Dieu
        $serviteur_de_dieu = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Si l'utilisateur n'est ni un Fidele, ni un Chantre, ni un Serviteur de Dieu
        if (!$fidele && !$chantre && !$serviteur_de_dieu) {
            return response()->json(['error' => 'Vous n\'êtes ni un Fidèle, ni un Chantre, ni un Serviteur de Dieu.'], 403);
        }

        // Mise à jour de l'église pour un Fidele
        if ($fidele) {
            $fidele->update([
                'church_id' => $churchId,
            ]);
        }

        // Mise à jour de l'église pour un Chantre
        if ($chantre) {
            $chantre->update([
                'church_id' => $churchId,
            ]);
        }

        // Mise à jour de l'église pour un Serviteur de Dieu
        if ($serviteur_de_dieu) {
            $serviteur_de_dieu->update([
                'church_id' => $churchId,
            ]);
        }

        return response()->json(['success' => 'Église sélectionnée avec succès !']);
    }










    public function edit($churchId)
    {
        // Récupérer l'église avec l'ID passé en paramètre
        $church = Church::find($churchId);

        if (!$church) {
            return response()->json(['error' => 'Église non trouvée.'], 404);
        }

        // Vérifier si l'utilisateur connecté est bien le propriétaire de l'église
        if ($church->owner_servant_id !== auth()->id()) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à modifier cette église.'], 403);
        }

        // Serviteur assigné à l'église
        $serviteur = ServiteurDeDieu::where('church_id', $church->id)
            ->where('is_assigned', true)
            ->first();

        // Récupérer les utilisateurs disponibles pour être assignés à l'église (exclure ceux déjà assignés)
        $users = User::whereHas('serviteurDeDieu', function ($query) {
            $query->where('is_main', false)
                ->where('is_assigned', false);
        })->get();

        // Récupérer les églises associées aux serviteurs de Dieu des utilisateurs
        $serviteursDeDieuChurchIds = [];
        foreach ($users as $user) {
            $serviteurDeDieu = ServiteurDeDieu::where('user_id', $user->id)->first();
            if ($serviteurDeDieu) {
                $serviteursDeDieuChurchIds[] = $serviteurDeDieu->church_id;
            }
        }

        // Vérifier si l'utilisateur possède l'église associée aux serviteurs de Dieu
        $userChurch = Church::where('owner_servant_id', auth()->id())
            ->whereIn('id', $serviteursDeDieuChurchIds)
            ->first();

        if ($userChurch) {
            // Récupérer les utilisateurs qui ne sont pas encore assignés à cette église
            $availableUsers = User::whereHas('serviteurDeDieu', function ($query) use ($church) {
                $query->where('church_id', $church->id)
                    ->where('is_assigned', false)
                    ->where('is_main', false);
            })->get();

            return response()->json([
                'church' => $church,
                'serviteur' => $serviteur,
                'users' => $availableUsers,
            ]);
        }

        return response()->json([
            'church' => $church,
            'serviteur' => $serviteur,
        ]);
    }






    public function update(Request $request, $churchId)
    {
        // Récupérer l'église par son ID
        $church = Church::find($churchId);

        if (!$church) {
            return response()->json(['error' => 'Église non trouvée'], 404);
        }

        // Validation des données d'entrée
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:10',
            'adresse' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation du logo
            'is_main' => 'required|boolean',
            'description' => 'nullable|string',
            'type_church' => 'nullable|string',
        ], [
            // Messages personnalisés
            'name.required' => 'Le champ nom est obligatoire.',
            'name.string' => 'Le champ nom doit être une chaîne de caractères.',
            'name.max' => 'Le champ nom ne doit pas dépasser :max caractères.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'L\'adresse email ne doit pas dépasser :max caractères.',
            'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser :max caractères.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être de type :values.',
            'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',
            'is_main.required' => 'Le champ "principal" est requis.',
            'is_main.boolean' => 'Le champ "principal" doit être vrai ou faux.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',
        ]);

        // Gestion du téléchargement du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo s'il existe
            if ($church->logo) {
                Storage::disk('public')->delete($church->logo);
            }

            // Génération du nom du nouveau logo
            $logoFile = $request->file('logo');
            $logoFilename = $validatedData['name'] . '_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoPath = $logoFile->storeAs('public/logos', $logoFilename);
            $validatedData['logo'] = $logoPath;
        }

        // Vérifier que l'utilisateur connecté est un serviteur de Dieu
        $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();

        if (!$serviteurDeDieu) {
            return response()->json(['error' => 'L\'utilisateur connecté n\'est pas un serviteur de Dieu.'], 403);
        }

        // Mise à jour des informations de l'église
        $church->update(array_merge($validatedData, [
            'owner_servant_id' => $serviteurDeDieu->id,
        ]));

        // Si un changement de serviteur est demandé
        if ($request->change_serviteur) {

            // Récupérer le serviteur actuellement assigné
            $serviteur = ServiteurDeDieu::where('church_id', $church->id)
                ->where('is_assigned', true)
                ->first();

            // Désassigner le serviteur actuel si il existe
            if ($serviteur) {
                $serviteur->is_assigned = 0;
                $serviteur->save();
            }

            // Récupérer et assigner le nouveau serviteur
            $newServiteurDeDieu = ServiteurDeDieu::where('user_id', $request->user_id)->first();

            if ($newServiteurDeDieu) {
                $newServiteurDeDieu->is_assigned = 1;
                $newServiteurDeDieu->save();
            } else {
                return response()->json(['error' => 'Le serviteur spécifié n\'existe pas.'], 404);
            }
        }

        // Retourner la réponse avec succès
        return response()->json(['success' => 'Église mise à jour avec succès.'], 200);
    }














    /**
     * Supprimer une église.
     */
    public function destroy($churchId)
    {
        // Récupérer l'église par son ID
        $church = Church::find($churchId);

        if (!$church) {
            return response()->json(['error' => 'Église non trouvée'], 404);
        }

        // Supprimer le logo de stockage s'il existe
        if ($church->logo) {
            Storage::disk('public')->delete($church->logo);
        }

        // Désassocier le serviteur de Dieu de l'église sans le supprimer
        $serviteur = ServiteurDeDieu::where('church_id', $church->id)->first();
        if ($serviteur) {
            // Dissocier le serviteur de l'église
            $serviteur->church_id = null;
            $serviteur->is_assigned = 0;
            $serviteur->is_main = 0; // Mettre à jour l'attribut si nécessaire
            $serviteur->save();
        }

        // Supprimer l'église
        $church->delete();

        // Retourner une réponse JSON de succès
        return response()->json(['success' => 'Église supprimée avec succès.'], 200);
    }




    /**
     * Réponse standardisée pour les succès.
     */
    private function successResponse($data, $message = 'Succès.', $status = 200): JsonResponse
    {
        return response()->json(['success' => $message, 'data' => $data], $status);
    }
}
