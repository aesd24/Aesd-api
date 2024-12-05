<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\ServiteurDeDieu;
use App\Models\Chantre;
use App\Models\Fidele;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;



class ChurchController extends Controller
{


    public function all_index()
    {
        $churches = Church::all();
        return view('churches.all_index', compact('churches'));
    }



    public function choisirEglise()
    {
        $eglises = Church::all(); // Récupère toutes les églises
        $user = Auth::user(); // Récupère l'utilisateur actuellement connecté

        // Vérification si l'utilisateur est un Fidèle ou un Chantre
        $fidele = Fidele::where('user_id', $user->id)->first();
        $chantre = Chantre::where('user_id', $user->id)->first();
        $serviteur_de_dieu = ServiteurDeDieu::where('user_id', $user->id)->first();


        // Récupère l'église associée à l'utilisateur, soit un Fidèle, soit un Chantre
        $selectedChurchId = null;
        if ($fidele) {
            $selectedChurchId = $fidele->church_id; // Si l'utilisateur est un Fidèle, récupérer l'église associée
        } elseif ($chantre) {
            $selectedChurchId = $chantre->church_id; // Si l'utilisateur est un Chantre, récupérer l'église associée
        } elseif ($serviteur_de_dieu) {

            // Vérifie si "isman" est false pour un Serviteur de Dieu
            // if ($serviteur_de_dieu->is_main == false) {
            if (!$serviteur_de_dieu->is_main) {
                $selectedChurchId = $serviteur_de_dieu->church_id; // Si l'utilisateur est un Chantre, récupérer l'église associée
            } else {
                return redirect()->back()->with('error', 'Votre statut actuel ne vous permet pas de choisir une église.');
            }

            $selectedChurchId = $serviteur_de_dieu->church_id; // Si l'utilisateur est un Chantre, récupérer l'église associée
        } else {
            // Si l'utilisateur n'est ni un Fidèle ni un Chantre, rediriger avec un message d'erreur
            return redirect()->back()->with('error', 'Vous devez être un Fidèle ou un Chantre pour choisir une église.');
        }

        return view('choice_church', compact('eglises', 'selectedChurchId')); // Envoie les églises et l'église sélectionnée à la vue
    }





    public function sauvegarderEgliseSelectionnee(Request $request)
    {
        $request->validate([
            'church_id' => ['required', 'exists:church,id'], // Valide que l'ID existe dans la table
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

        // Si l'utilisateur n'est ni un Fidele ni un Chantre
        if (!$fidele && !$chantre && !$serviteur_de_dieu) {
            // Redirige vers la page précédente avec un message d'erreur
            return back()->with('error', 'Vous n\'êtes ni un Fidele ni un Chantre.');
        }


        // Si l'utilisateur est un Fidele
        if ($fidele) {

            $User_fidele = Fidele::where('church_id', $churchId)
                ->with('user') // Charger la relation 'user' pour accéder aux informations de l'utilisateur
                ->first();

            if ($User_fidele) {
                $User_fidele->church_id = null;
                $User_fidele->save(); // Sauvegarder les modifications
            }

            // Récupérer le nouveau fidele assigné
            $Userfidele = Fidele::where('user_id', $user->id)->first();

            // Vérifier si le fidele existe
            if ($Userfidele) {
                // Associer le nouveau fidele à l'église
                $Userfidele->church_id = $churchId;
                $Userfidele->save(); // Sauvegarder les modifications
            } else {
                // Gérer le cas où le nouveau serviteur n'existe pas
                return redirect()->back()->with('error', 'Le fidele spécifié n\'existe pas.');
            }
        }


        // Si l'utilisateur est un Chantre
        if ($chantre) {

            $User_chanttre = Chantre::where('church_id', $churchId)
                ->with('user') // Charger la relation 'user' pour accéder aux informations de l'utilisateur
                ->first();

            if ($User_chanttre) {
                $User_chanttre->church_id = null;
                $User_chanttre->save(); // Sauvegarder les modifications
            }

            // Récupérer le nouveau chantre assigné
            $Userchantre = Chantre::where('user_id', $user->id)->first();

            // Vérifier si le chantre existe
            if ($Userchantre) {
                // Associer le nouveau chantre à l'église
                $Userchantre->church_id = $churchId;
                $Userchantre->save(); // Sauvegarder les modifications
            } else {
                // Gérer le cas où le nouveau serviteur n'existe pas
                return redirect()->back()->with('error', 'Le chantre spécifié n\'existe pas.');
            }
        }


        // Si l'utilisateur est un serviteur de Dieu
        if ($serviteur_de_dieu) {

            $Serviteur =  ServiteurDeDieu::where('church_id', $churchId)
                ->with('user') // Charger la relation 'user' pour accéder aux informations de l'utilisateur
                ->first();

            if ($Serviteur) {
                $Serviteur->church_id = null;
                $Serviteur->save(); // Sauvegarder les modifications
            }

            // Récupérer le nouveau chantre assigné
            $UserServiteur = ServiteurDeDieu::where('user_id', $user->id)->first();

            // Vérifier si le chantre existe
            if ($UserServiteur) {
                // Associer le nouveau chantre à l'église
                $UserServiteur->church_id = $churchId;
                $UserServiteur->save(); // Sauvegarder les modifications
            } else {
                // Gérer le cas où le nouveau serviteur n'existe pas
                return redirect()->back()->with('error', 'Le chantre spécifié n\'existe pas.');
            }
        }

        return redirect()->back()->with('success', "Église sélectionnée avec succès !");
    }




    public function index()
    {
        $user = auth()->user(); // Utilisateur actuellement connecté

        // Récupérer le ServiteurDeDieu correspondant à l'utilisateur
        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Vérifier si le serviteur existe
        if (!$serviteur) {
            // Si aucun serviteur n'est trouvé, gérer le cas, par exemple redirection avec un message d'erreur
            return redirect()->back()->with('error', 'Aucun serviteur associé à cet utilisateur.');
        }

        // Récupérer les églises créées par l'utilisateur connecté
        $churches = Church::where('owner_servant_id', $serviteur->id)->get();

        // Récupérer l'église associée au serviteur
        $churchesSecondaires = Church::find($serviteur->church_id); // Utilisez find pour récupérer l'église unique

        return view('churches.index', compact('churches', 'churchesSecondaires'));
    }



    /**
     * Afficher le formulaire de création d'une nouvelle église.
     */
    public function create()
    {
        return view('churches.create');
    }

    /**
     * Enregistrer une nouvelle église.
     */
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
            // 'categorie' => 'nullable|string',
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




        $idCardRectoFile = $request->file('logo');
        $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
        $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);


        $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();

        if (!$serviteurDeDieu) {
            return redirect()->back()->withErrors('L\'utilisateur connecté n\'est pas un serviteur de Dieu.');
        }

        // Enregistrement avec l'ID de l'utilisateur connecté
        Church::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'adresse' => $validatedData['adresse'] ?? null,
            'logo' => $logoPath ?? null, // Si le logo est téléchargé, son chemin est enregistré
            'is_main' => $validatedData['is_main'],
            'description' => $validatedData['description'] ?? null,
            'owner_servant_id' => $serviteurDeDieu->id, // Utilisez l'ID du serviteur de Dieu
            'type_church' => $validatedData['type_church'] ?? null,
            // 'categorie' => $validatedData['categorie'] ?? null,
        ]);

        // Redirection avec message de succès
        return redirect()->route('churches.index')->with('success', 'Église créée avec succès.');
    }

    /**
     * Afficher les détails d'une église.
     */
    public function show(Church $church)
    {
        return view('churches.show', compact('church'));
    }




    public function edit(Church $church)
    {
        // Récupérer les utilisateurs associés à un ServiteurDeDieu avec 'is_main' à false
        $users = User::whereHas('serviteurDeDieu', function ($query) {
            $query->where('is_main', false);
        })->get();


        $serviteursDeDieuChurchIds = [];  // Tableau pour stocker les `church_id`

        foreach ($users as $user) {
            // Récupérer le ServiteurDeDieu correspondant à l'utilisateur
            $serviteurDeDieu = ServiteurDeDieu::where('user_id', $user->id)->first();

            if ($serviteurDeDieu) {
                // Ajouter le `church_id` du ServiteurDeDieu au tableau
                $serviteursDeDieuChurchIds[] = $serviteurDeDieu->church_id;
            }
        }


        $churchIdOwnedByUser = Church::where('owner_servant_id', auth()->id())->value('id');

        // Vérifier si l'église avec owner_servant_id = 1 et id = 2 existe
        $exists = Church::where('owner_servant_id',  $churchIdOwnedByUser)
            ->where('id', $serviteursDeDieuChurchIds[0])
            ->exists();



        // Récupérer le ServiteurDeDieu associé à l'église
        $serviteur = ServiteurDeDieu::where('church_id', $church->id)
            ->with('user')  // Charger la relation 'user' directement
            ->first();


        if (!$exists) {
            // Passer les utilisateurs et l'église à la vue
            return view('churches.edit', compact('church', 'serviteur'));
        }


        // Passer les utilisateurs et l'église à la vue
        return view('churches.edit', compact('church', 'users', 'serviteur'));
    }




    /**
     * Mettre à jour les informations d'une église.
     */
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
            // 'categorie' => 'nullable|string',
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
            return redirect()->back()->withErrors('L\'utilisateur connecté n\'est pas un serviteur de Dieu.');
        }

        // Mise à jour des informations de l'église
        $church->update(array_merge($validatedData, [
            'owner_servant_id' => $serviteurDeDieu->id, // Utilise l'ID du serviteur de Dieu
        ]));


        if ($request->change_serviteur) {

            // Récupérer le serviteur actuellement assigné à l'église
            $serviteur = ServiteurDeDieu::where('church_id', $church->id)
                ->with('user') // Charger la relation 'user' pour accéder aux informations de l'utilisateur
                ->first();

            // Vérifier si un serviteur est assigné à l'église
            if ($serviteur) {
                // Désassocier le serviteur actuel de l'église
                $serviteur->church_id = null;
                $serviteur->save(); // Sauvegarder les modifications
            }


            // Récupérer le nouveau serviteur assigné via le champ 'user_id' de la requête
            $serviteurDeDieu = ServiteurDeDieu::where('user_id', $request->user_id)->first();

            // Vérifier si le nouveau serviteur existe
            if ($serviteurDeDieu) {
                // Associer le nouveau serviteur à l'église
                $serviteurDeDieu->church_id = $church->id;
                $serviteurDeDieu->save(); // Sauvegarder les modifications
            } else {
                // Gérer le cas où le nouveau serviteur n'existe pas
                return redirect()->back()->with('error', 'Le serviteur spécifié n\'existe pas.');
            }
        }

        // Redirection avec message de succès
        return redirect()->route('churches.index')->with('success', 'Église mise à jour avec succès.');
    }




    /**
     * Supprimer une église.
     */
    public function destroy(Church $church)
    {
        // Supprimez le logo de stockage s'il existe
        if ($church->logo) {
            Storage::disk('public')->delete($church->logo);
        }

        $church->delete();
        return redirect()->route('churches.index')->with('success', 'Église supprimée avec succès.');
    }
}
