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

        // Vérification si l'utilisateur est un serviteur de Dieu
        $serviteur_de_dieu = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Si l'utilisateur n'est ni un Fidele ni un Chantre
        if (!$fidele && !$chantre && !$serviteur_de_dieu) {
            // Redirige vers la page précédente avec un message d'erreur
            return back()->with('error', 'Vous n\'êtes ni un Fidele ni un Chantre.');
        }


        // Si l'utilisateur est un Fidele
        if ($fidele) {

            $fidele->update([
                'church_id' => $churchId,
            ]);
        }


        // Si l'utilisateur est un Chantre
        if ($chantre) {

            $chantre->update([
                'church_id' => $churchId,
            ]);
        }


        // Si l'utilisateur est un serviteur de Dieu
        if ($serviteur_de_dieu) {

            $serviteur_de_dieu->update([
                'church_id' => $churchId,
            ]);
        }

        return redirect()->back()->with('success', "Église sélectionnée avec succès !");
    }








    public function index()
    {
        $user = auth()->user();

        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();

        if (!$serviteur) {
            return redirect()->back()->with('error', 'Aucun serviteur associé à cet utilisateur.');
        }

        $churches = Church::where('owner_servant_id', $serviteur->id)->get();

        // Récupérer les IDs des églises secondaires
        $secondaryChurchIds = $churches->pluck('main_church_id')->filter()->unique();

        // Récupérer les églises secondaires en une seule requête
        $churchesSecondaires = Church::whereIn('id', $secondaryChurchIds)->get();



        // Vérifier si $churchesSecondaires n'est pas vide
        if ($churchesSecondaires->isNotEmpty()) {
            return view('churches.index', compact('churches', 'churchesSecondaires'));
        } else {

            return view('churches.index', compact('churches'));
        }
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

            // 'attestation_file_path' => 'nullable|file|mimes:pdf|max:2048', // Validation pour un fichier PDF

            // Validation conditionnelle pour l'attestation_file_path
            'attestation_file_path' => 'nullable|file|mimes:pdf|max:2048|required_if:is_main,true', // Requis si is_main est vrai
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


            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être de type :values.', // Gère les types de fichiers autorisés
            'logo.max' => 'Le logo ne doit pas dépasser :max Ko.',

            'is_main.required' => 'Le champ "principal" est requis.',
            'is_main.boolean' => 'Le champ "principal" doit être vrai ou faux.',

            'description.string' => 'La description doit être une chaîne de caractères.',

            'type_church.string' => 'Le type d\'église doit être une chaîne de caractères.',

            // 'categorie.string' => 'La catégorie doit être une chaîne de caractères.',


            // Messages personnalisés
            'attestation_file_path.file' => 'Le fichier d\'attestation doit être un fichier valide.',
            'attestation_file_path.mimes' => 'L\'attestation doit être un fichier PDF.',
            'attestation_file_path.max' => 'L\'attestation ne doit pas dépasser :max Ko.',
            'attestation_file_path.required_if' => 'L\'attestation est requise lorsque l\'église est principale.',
        ]);



        if ($validatedData['is_main']) {


            $idCardRectoFile = $request->file('logo');
            $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
            $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);


            // Gestion du fichier attestation
            $attestationPath = null;
            if ($request->hasFile('attestation_file_path')) {
                $attestationFile = $request->file('attestation_file_path');
                $attestationFilename = $validatedData['name'] . '_attestation_' . time() . '.' . $attestationFile->getClientOriginalExtension();
                $attestationPath = $attestationFile->storeAs('public/attestations', $attestationFilename);
            }

            $churchIdOwnedByUser = Church::where('owner_servant_id', auth()->id())->value('id');
            if ($churchIdOwnedByUser) {
                return redirect()->back()->withErrors('Vous avez déjà une église principale.');
            }

            $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();
            if (!$serviteurDeDieu) {
                return redirect()->back()->withErrors('Aucun serviteur trouvé pour l\'utilisateur connecté.');
            }

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
                'attestation_file_path' => $attestationPath ?? null, // Enregistrer le chemin de l'attestation
            ]);

            if (!$newChurch) {
                return redirect()->back()->withErrors('Erreur lors de la création de l\'église.');
            }

            // Rafraîchir l'objet
            $newChurch->refresh();

            // Mise à jour directe
            try {
                $newChurch->update([
                    'main_church_id' => $newChurch->id,
                ]);

                // dd( $serviteurDeDieu);

                // Mettre à jour le champ `church_id` pour le serviteur connecté
                $serviteurDeDieu->update([
                    'church_id' =>  $newChurch->id,
                    'is_assigned' => 1,
                    'is_main' => 1,
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors('Erreur lors de la mise à jour : ' . $e->getMessage());
            }

            return redirect()->route('churches.index')->with('success', 'Église principale créée avec succès.');
        } else {

            $idCardRectoFile = $request->file('logo');
            $rectoFilename = $validatedData['name'] . '_' . time() . '.' . $idCardRectoFile->getClientOriginalExtension();
            $logoPath = $idCardRectoFile->storeAs('public/logos', $rectoFilename);

            // Récupérer le modèle du serviteur connecté
            $serviteurDeDieu = ServiteurDeDieu::where('user_id', auth()->id())->first();

            if (!$serviteurDeDieu) {
                return redirect()->back()->withErrors('Aucun serviteur trouvé pour l\'utilisateur connecté.');
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
        // Récupérer l'ID de l'église possédée par l'utilisateur connecté
        $churchIdOwnedByUser = Church::where('owner_servant_id', auth()->id())->value('id');

        if (!$churchIdOwnedByUser) {
            return redirect()->back()->withErrors('Vous ne possédez pas d\'église associée.');
        }

        // Vérifier si l'utilisateur possède l'église qu'il essaie d'éditer
        if ($church->owner_servant_id !== auth()->id()) {
            return redirect()->back()->withErrors('Vous n\'êtes pas autorisé à modifier cette église.');
        }


        //serviteur assigner à une église spécifique
        $serviteur = ServiteurDeDieu::where('church_id', $church->id)
            ->where('is_assigned', true) // Ajouter la condition pour is_assigned
            // ->with('user') // Charger la relation 'user'
            ->first();

        $users = User::whereHas('serviteurDeDieu', function ($query) {
            $query->where('is_main', false)
                ->where('is_assigned', false); // Ajouter la condition pour is_assigned
        })->get();


        if ($users) {
            // Récupérer les IDs des églises associées aux ServiteursDeDieu des utilisateurs
            $serviteursDeDieuChurchIds = [];
            foreach ($users as $user) {
                $serviteurDeDieu = ServiteurDeDieu::where('user_id', $user->id)->first();
                if ($serviteurDeDieu) {
                    $serviteursDeDieuChurchIds[] = $serviteurDeDieu->church_id;
                }
            }
        }


        $user_church = Church::where('owner_servant_id', auth()->id())
            ->whereIn('id', $serviteursDeDieuChurchIds)
            ->first();


        if ($user_church) {

            $users = User::whereHas('serviteurDeDieu', function ($query) use ($church) {
                $query->where('church_id', $church->id) // Filtrer par l'ID de l'église
                    ->where('is_assigned', false)      // Vérifier que le serviteur n'est pas assigné
                    ->where('is_main', false);         // Vérifier que ce n'est pas un serviteur principal
            })->get();

            // dd($users);

            if ($users) {
                return view('churches.edit', compact('church', 'users', 'serviteur'));
            } else {

                return view('churches.edit', compact('church', 'serviteur'));
            }
        }
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
            // $serviteur = ServiteurDeDieu::where('church_id', $church->id)
            //     ->with('user') // Charger la relation 'user' pour accéder aux informations de l'utilisateur
            //     ->first();


            $serviteur = ServiteurDeDieu::where('church_id', $church->id)
                ->where('is_assigned', true) // Ajouter la condition pour is_assigned
                ->with('user') // Charger la relation 'user'
                ->first();



            // Vérifier si un serviteur est assigné à l'église
            if ($serviteur) {
                // Désassocier le serviteur actuel
                $serviteur->is_assigned = 0;
                $serviteur->save(); // Sauvegarder les modifications
            }


            // Récupérer le nouveau serviteur assigné via le champ 'user_id' de la requête
            $serviteurDeDieu = ServiteurDeDieu::where('user_id', $request->user_id)->first();


            // Vérifier si le nouveau serviteur existe
            if ($serviteurDeDieu) {
                // Associer le nouveau serviteur à l'église
                $serviteurDeDieu->is_assigned = 1;
                $serviteurDeDieu->save(); // Sauvegarder les modifications
            } else {
                // Gérer le cas où le nouveau serviteur n'existe pas
                return redirect()->back()->with('error', 'Le serviteur spécifié n\'existe pas.');
            }
        }

        // Redirection avec message de succès
        return redirect()->route('churches.index')->with('success', 'Église mise à jour avec succès.');
    }





    public function destroy(Church $church)
    {
        // Supprimer le logo de stockage s'il existe
        if ($church->logo) {
            Storage::disk('public')->delete($church->logo);
        }

        // Désassocier le serviteur de Dieu de l'église sans le supprimer
        $serviteur = ServiteurDeDieu::where('church_id', $church->id)->first();
        if ($serviteur) {
            // Vous pouvez soit le dissocier (si vous ne voulez pas le supprimer), soit juste changer son statut
            $serviteur->church_id = null; // Dissocier le serviteur de l'église
            $serviteur->is_assigned = 0;
            $serviteur->is_main = 0; // Mettre à jour l'attribut si nécessaire
            $serviteur->save();
        }

        // Supprimer l'église
        $church->delete();

        return redirect()->route('churches.index')->with('success', 'Église supprimée avec succès.');
    }
}
