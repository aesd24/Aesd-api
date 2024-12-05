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
    /**
     * Afficher la liste des églises.
     */
    // public function index(): JsonResponse
    // {
    //     $churches = Church::with(['serviteursDeDieu', 'fideles', 'chantres'])->get();
    //     return $this->successResponse($churches);
    // }




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

    /**
     * Enregistrer une nouvelle église.
     */
    // public function store(StoreChurchRequest $request): JsonResponse
    // {
    //     // Gestion du téléchargement du logo
    //     $logoPath = $this->handleLogoUpload($request);

    //     // Vérification que l'utilisateur est un serviteur de Dieu
    //     $serviteurDeDieu = $this->getAuthenticatedServiteurDeDieu();

    //     if (!$serviteurDeDieu) {
    //         return $this->errorResponse('L\'utilisateur connecté n\'est pas un serviteur de Dieu.', 403);
    //     }

    //     // Enregistrement avec l'ID du serviteur de Dieu
    //     $church = Church::create(array_merge($request->validated(), [
    //         'logo' => $logoPath,
    //         'owner_servant_id' => $serviteurDeDieu->id,
    //     ]));

    //     return $this->successResponse($church, 'Église créée avec succès.', 201);
    // }




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
        ],  [
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
            'logo.mimes' => 'Le logo doit être de type :values.', // Gère les types de fichiers autorisés
            'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',

            'is_main.required' => 'Le champ "principal" est requis.',
            'is_main.boolean' => 'Le champ "principal" doit être vrai ou faux.',

            'description.string' => 'La description doit être une chaîne de caractères.',

            'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',

            // 'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
        ]);

        // Vérifier si le logo est fourni
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $idCardRectoFile = $request->file('logo');
            $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
            $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);
        }

        // Récupérer le ServiteurDeDieu associé à l'utilisateur connecté
        $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();

        if (!$serviteurDeDieu) {
            return response()->json([
                'message' => 'L\'utilisateur connecté n\'est pas un serviteur de Dieu.'
            ], 403); // 403 Forbidden
        }

        // Créer une nouvelle église
        $church = Church::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'adresse' => $validatedData['adresse'] ?? null,
            'logo' => $logoPath, // Si le logo est téléchargé, son chemin est enregistré
            'is_main' => $validatedData['is_main'],
            'description' => $validatedData['description'] ?? null,
            'owner_servant_id' => $serviteurDeDieu->id, // Utilisez l'ID du serviteur de Dieu
            'type_church' => $validatedData['type_church'] ?? null,
        ]);

        // Retourner une réponse JSON avec succès
        return response()->json([
            'message' => 'Église créée avec succès.',
            'data' => $church
        ], 201); // 201 Created
    }






    public function serviteur_sécondaire(Church $church)
    {
        // Récupérer les utilisateurs associés à un ServiteurDeDieu avec 'is_main' à false
        $users = User::whereHas('serviteurDeDieu', function ($query) {
            $query->where('is_main', false);
        })->get();

        // Récupérer les `church_id` associés aux utilisateurs
        $serviteursDeDieuChurchIds = $users->map(function ($user) {
            $serviteurDeDieu = ServiteurDeDieu::where('user_id', $user->id)->first();
            return $serviteurDeDieu ? $serviteurDeDieu->church_id : null;
        })->filter()->toArray(); // Filtrer les valeurs nulles et convertir en tableau

        // Récupérer l'ID de l'église associée au propriétaire connecté
        $churchIdOwnedByUser = Church::where('owner_servant_id', auth()->id())->value('id');

        // Vérifier si une église spécifique existe pour cet utilisateur
        $exists = Church::where('owner_servant_id', $churchIdOwnedByUser)
            ->whereIn('id', $serviteursDeDieuChurchIds)
            ->exists();

        // Récupérer le `ServiteurDeDieu` associé à l'église
        $serviteur = ServiteurDeDieu::where('church_id', $church->id)
            ->with('user') // Charger la relation utilisateur
            ->first();

        // Si l'église n'est pas accessible à cet utilisateur, retourner une erreur
        if (!$exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à modifier cette église.',
                'church' => $church,
                'serviteur' => $serviteur,
            ], 403);
        }

        // Retourner les détails de l'église, des utilisateurs, et du serviteur
        return response()->json([
            'status' => 'success',
            'message' => 'Détails de l\'église récupérés avec succès.',
            'church' => $church,
            'users' => $users,
            'serviteur' => $serviteur,
        ]);
    }




    public function choisirEglise()
    {
        $eglises = Church::all(); // Récupère toutes les églises
        $user = Auth::user(); // Récupère l'utilisateur actuellement connecté

        // Vérification si l'utilisateur est un Fidèle ou un Chantre
        $fidele = Fidele::where('user_id', $user->id)->first();
        $chantre = Chantre::where('user_id', $user->id)->first();
        $serviteurDeDieu = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Initialisation de l'église associée à l'utilisateur
        $selectedChurchId = null;

        if ($fidele) {
            $selectedChurchId = $fidele->church_id; // L'utilisateur est un Fidèle
        } elseif ($chantre) {
            $selectedChurchId = $chantre->church_id; // L'utilisateur est un Chantre
        } elseif ($serviteurDeDieu) {
            if (!$serviteurDeDieu->is_main) { // Vérifie que "is_main" est false
                $selectedChurchId = $serviteurDeDieu->church_id;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre statut actuel ne vous permet pas de choisir une église.'
                ], 403); // Statut HTTP 403 Forbidden
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être un Fidèle ,  un Chantre , serviteur pour choisir une église.'
            ], 403); // Statut HTTP 403 Forbidden
        }

        // Retourner les données des églises et de l'église sélectionnée
        return response()->json([
            'success' => true,
            'data' => [
                'eglises' => $eglises,
                'selected_church_id' => $selectedChurchId
            ]
        ], 200); // Statut HTTP 200 OK
    }


    public function sauvegarderEgliseSelectionnee(Request $request)
    {
        $request->validate([
            'church_id' => ['required', 'exists:churches,id'], // Valide que l'ID existe dans la table
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

        // Vérification si l'utilisateur est un Serviteur de Dieu
        $serviteurDeDieu = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Si l'utilisateur n'est ni un Fidele, ni un Chantre, ni un Serviteur de Dieu
        if (!$fidele && !$chantre && !$serviteurDeDieu) {
            return response()->json(['error' => 'Vous devez être un Fidèle, un Chantre ou un Serviteur de Dieu pour sélectionner une église.'], 403);
        }

        // Met à jour l'église pour un Fidèle
        if ($fidele) {
            $fidele->church_id = $churchId;
            $fidele->save();
        }

        // Met à jour l'église pour un Chantre
        if ($chantre) {
            $chantre->church_id = $churchId;
            $chantre->save();
        }

        // Met à jour l'église pour un Serviteur de Dieu
        if ($serviteurDeDieu) {
            $serviteurDeDieu->church_id = $churchId;
            $serviteurDeDieu->save();
        }

        // Réponse de succès
        return response()->json(['success' => 'Église sélectionnée avec succès !']);
    }













    /**
     * Mettre à jour les informations d'une église.
     */
    // public function update(StoreChurchRequest $request, Church $church): JsonResponse
    // {
    //     // Gestion du téléchargement du logo
    //     $logoPath = $this->handleLogoUpload($request, $church);

    //     // Vérification que l'utilisateur connecté est un serviteur de Dieu
    //     $serviteurDeDieu = $this->getAuthenticatedServiteurDeDieu();

    //     if (!$serviteurDeDieu) {
    //         return $this->errorResponse('L\'utilisateur connecté n\'est pas un serviteur de Dieu.', 403);
    //     }

    //     // Mise à jour des informations de l'église
    //     $church->update(array_merge($request->validated(), [
    //         'logo' => $logoPath,
    //         'owner_servant_id' => $serviteurDeDieu->id,
    //     ]));

    //     return $this->successResponse($church, 'Église mise à jour avec succès.');
    // }




    public function update(Request $request, Church $church)
    {
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
            'logo.mimes' => 'Le logo doit être de type :values.', // Gère les types de fichiers autorisés
            'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',

            'is_main.required' => 'Le champ "principal" est requis.',
            'is_main.boolean' => 'Le champ "principal" doit être vrai ou faux.',

            'description.string' => 'La description doit être une chaîne de caractères.',

            'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',

            // 'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
        ]);
        // Gestion du téléchargement du logo
        if ($request->hasFile('logo')) {
            // Supprimez l'ancien logo s'il existe
            if ($church->logo) {
                Storage::disk('public')->delete($church->logo);
            }

            // Génération du nom du nouveau logo
            $idCardRectoFile = $request->file('logo');
            $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
            $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);
            $validatedData['logo'] = $logoPath; // Met à jour le chemin du logo dans les données validées
        }

        // Vérification que l'utilisateur connecté est un serviteur de Dieu
        $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();

        if (!$serviteurDeDieu) {
            return response()->json([
                'status' => 'error',
                'message' => 'L\'utilisateur connecté n\'est pas un serviteur de Dieu.',
            ], 403);
        }

        // Mise à jour des informations de l'église
        $church->update(array_merge($validatedData, [
            'owner_servant_id' => $serviteurDeDieu->id, // Utilise l'ID du serviteur de Dieu
        ]));

        // Si un changement de serviteur est demandé  bolean 1 ou 0 true or false faire la mise à jour avec put
        if ($request->change_serviteur) {
            // Récupérer le serviteur actuellement assigné à l'église
            $serviteur = ServiteurDeDieu::where('church_id', $church->id)->first();

            // Désassocier l'ancien serviteur, s'il existe
            if ($serviteur) {
                $serviteur->church_id = null;
                $serviteur->save();
            }

            // Associer le nouveau serviteur
            $newServiteurDeDieu = ServiteurDeDieu::where('user_id', $request->user_id)->first();

            if ($newServiteurDeDieu) {
                $newServiteurDeDieu->church_id = $church->id;
                $newServiteurDeDieu->save();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Le serviteur spécifié n\'existe pas.',
                ], 404);
            }
        }

        // Retourner une réponse JSON de succès
        return response()->json([
            'status' => 'success',
            'message' => 'Église mise à jour avec succès.',
            'church' => $church,
        ], 200);
    }

    /**
     * Supprimer une église.
     */
    public function destroy(Church $church): JsonResponse
    {
        // Supprimez le logo de stockage s'il existe
        if ($church->logo) {
            Storage::disk('public')->delete($church->logo);
        }

        $church->delete();
        return $this->successResponse(null, 'Église supprimée avec succès.');
    }

    /**
     * Gérer le téléchargement du logo.
     */
    private function handleLogoUpload($request, Church $church = null): ?string
    {
        if ($request->hasFile('logo')) {
            // Supprimez l'ancien logo s'il existe et que c'est une mise à jour
            if ($church && $church->logo) {
                Storage::disk('public')->delete($church->logo);
            }

            $logoFile = $request->file('logo');
            $filename = $request->input('name') . '_' . time() . '.' . $logoFile->getClientOriginalExtension();
            return $logoFile->storeAs('public/logos', $filename);
        }
        return $church ? $church->logo : null; // Conserve le logo existant s'il n'y a pas de nouveau fichier
    }

    /**
     * Obtenir le Serviteur de Dieu authentifié.
     */
    private function getAuthenticatedServiteurDeDieu()
    {
        return ServiteurDeDieu::where('user_id', auth()->id())->first();
    }

    /**
     * Réponse standardisée pour les succès.
     */
    private function successResponse($data, $message = 'Succès.', $status = 200): JsonResponse
    {
        return response()->json(['success' => $message, 'data' => $data], $status);
    }

    /**
     * Réponse standardisée pour les erreurs.
     */
    private function errorResponse($message, $status = 400): JsonResponse
    {
        return response()->json(['error' => $message], $status);
    }
}
