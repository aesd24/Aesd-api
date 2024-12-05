<?php

namespace App\Http\Controllers;

use App\Models\Ceremonie;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiteurDeDieu;

class CeremonieController extends Controller
{
    // public function index()
    // {
    //     $ceremonies = Ceremonie::with('churches')->get();
    //     return view('ceremonies.index', compact('ceremonies'));
    // }




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
            $query->where('owner_servant_id', $serviteur->id);
        })->with('churches')->get();

        return view('ceremonies.index', compact('ceremonies'));
    }




    // public function create()
    // {
    //     $churches = Church::all(); // Récupère toutes les églises pour afficher dans le formulaire
    //     return view('ceremonies.create', compact('churches'));
    // }



    public function create()
    {
        $user = auth()->user(); // Utilisateur actuellement connecté

        // Récupérer le ServiteurDeDieu correspondant à l'utilisateur
        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Vérifier si un ServiteurDeDieu est trouvé
        if (!$serviteur) {
            return redirect()->back()->with('error', 'Aucun serviteur associé à cet utilisateur.');
        }

        // Récupérer les églises créées par l'utilisateur connecté
        $churches = Church::where('owner_servant_id', $serviteur->id)->get();

        return view('ceremonies.create', compact('churches'));
    }








    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'media' => 'nullable|string',
    //         'event_date' => 'required|date',
    //         'churches' => 'required|array', // Validation pour les églises sélectionnées
    //         'churches.*' => 'exists:churches,id', // Validation pour chaque église sélectionnée
    //         'periode_times' => 'required|array',
    //         'periode_times.*' => 'string',
    //     ]);

    //     $ceremonie = Ceremonie::create($validatedData);

    //     // Utilisation de sync() pour associer les églises avec les périodes de temps
    //     $syncData = [];
    //     foreach ($request->churches as $index => $church_id) {
    //         $syncData[$church_id] = ['periode_time' => $request->periode_times[$index]];
    //     }
    //     $ceremonie->churches()->sync($syncData);

    //     return redirect()->route('ceremonies.index')->with('success', 'Cérémonie créée avec succès.');
    // }


    // public function store(Request $request)
    // {
    //     // Valider les données entrantes
    //     $validatedData = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'media' => 'nullable|string',
    //         'event_date' => 'required|date',
    //         'churches' => 'required|array', // Validation pour les églises sélectionnées
    //         'churches.*' => 'exists:church,id', // Correction pour correspondre au nom de table
    //         'periode_times' => 'required|array',
    //         'periode_times.*' => 'string',
    //     ]);

    //     // Créer la cérémonie avec les données validées
    //     $ceremonie = Ceremonie::create($validatedData);

    //     // Préparer les données pour la table pivot avec les périodes de temps
    //     $syncData = [];
    //     foreach ($request->churches as $index => $church_id) {
    //         // S'assurer que la période de temps correspond à l'église
    //         $syncData[$church_id] = ['periode_time' => $request->periode_times[$index]];
    //     }

    //     // Associer les églises et les périodes à la cérémonie
    //     $ceremonie->churches()->sync($syncData);

    //     return redirect()->route('ceremonies.index')->with('success', 'Cérémonie créée avec succès.');
    // }



    // public function store(Request $request)
    // {
    //     // Valider les données entrantes
    //     $validatedData = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'media' => 'nullable|string',
    //         'event_date' => 'required|date',
    //         'churches' => 'required|array', // Validation pour les églises sélectionnées
    //         'churches.*' => 'exists:church,id', // Vérifiez que l'ID de l'église existe
    //         'periode_time' => 'required|date', // Validation pour une période unique
    //     ]);

    //     // Créer la cérémonie avec les données validées
    //     $ceremonie = Ceremonie::create([
    //         'title' => $validatedData['title'],
    //         'description' => $validatedData['description'],
    //         // 'media' => $validatedData['media'],
    //         'event_date' => $validatedData['event_date'],
    //     ]);

    //     // Préparer les données pour la table pivot avec une période unique
    //     $syncData = [];
    //     foreach ($request->churches as $church_id) {
    //         // Associer chaque église avec la même période
    //         $syncData[$church_id] = ['periode_time' => $request->periode_time];
    //     }

    //     // Associer les églises et la période à la cérémonie
    //     $ceremonie->churches()->sync($syncData);

    //     return redirect()->route('ceremonies.index')->with('success', 'Cérémonie créée avec succès.');
    // }





    public function store(Request $request)
    {
        // Valider les données entrantes
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // Validation pour le champ media
            'event_date' => 'required|date',
            'churches' => 'required|array', // Validation pour les églises sélectionnées
            'churches.*' => 'exists:church,id', // Vérifiez que l'ID de l'église existe
            'periode_time' => 'required|date', // Validation pour une période unique
        ]);

        // Gérer le téléchargement du fichier média
        $mediaPath = null; // Initialiser le chemin du média
        if ($request->hasFile('media')) {
            $mediaFile = $request->file('media');
            // Définir le chemin où le fichier sera stocké
            $mediaPath = $mediaFile->store('media', 'public'); // 'media' est le dossier et 'public' est le disque
        }

        // Créer la cérémonie avec les données validées
        $ceremonie = Ceremonie::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'media' => $mediaPath, // Enregistrer le chemin du fichier média
            'event_date' => $validatedData['event_date'],
        ]);

        // Préparer les données pour la table pivot avec une période unique
        $syncData = [];
        foreach ($request->churches as $church_id) {
            // Associer chaque église avec la même période
            $syncData[$church_id] = ['periode_time' => $request->periode_time];
        }

        // Associer les églises et la période à la cérémonie
        $ceremonie->churches()->sync($syncData);

        return redirect()->route('ceremonies.index')->with('success', 'Cérémonie créée avec succès.');
    }



    public function show($id)
    {
        // Récupérer la cérémonie par ID
        $ceremonie = Ceremonie::findOrFail($id);

        // Passer l'objet cérémonie à la vue
        return view('ceremonies.show', compact('ceremonie'));
    }


    // public function edit(Ceremonie $ceremonie)
    // {
    //     $churches = Church::all(); // Récupère toutes les églises pour l'édition
    //     return view('ceremonies.edit', compact('ceremonie', 'churches'));
    // }


    public function edit($id)
    {
        // Récupérer la cérémonie par ID
        $ceremonie = Ceremonie::findOrFail($id);

        $user = auth()->user(); // Utilisateur actuellement connecté

        // Récupérer le ServiteurDeDieu correspondant à l'utilisateur
        $serviteur = ServiteurDeDieu::where('user_id', $user->id)->first();

        // Vérifier si un ServiteurDeDieu est trouvé
        if (!$serviteur) {
            return redirect()->back()->with('error', 'Aucun serviteur associé à cet utilisateur.');
        }

        // Récupérer les églises créées par l'utilisateur connecté
        $churches = Church::where('owner_servant_id', $serviteur->id)->get();

        // Passer l'objet cérémonie et la liste des églises à la vue
        return view('ceremonies.edit', compact('ceremonie', 'churches'));
    }


    // public function update(Request $request, Ceremonie $ceremonie)
    // {
    //     $validatedData = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'media' => 'nullable|string',
    //         'event_date' => 'required|date',
    //         'churches' => 'required|array',
    //         'churches.*' => 'exists:churches,id',
    //         'periode_times' => 'required|array',
    //         'periode_times.*' => 'string',
    //     ]);

    //     $ceremonie->update($validatedData);

    //     // Synchroniser les églises et les périodes de temps avec sync()
    //     $syncData = [];
    //     foreach ($request->churches as $index => $church_id) {
    //         $syncData[$church_id] = ['periode_time' => $request->periode_times[$index]];
    //     }
    //     $ceremonie->churches()->sync($syncData);

    //     return redirect()->route('ceremonies.index')->with('success', 'Cérémonie mise à jour avec succès.');
    // }

    public function update(Request $request, $id)
    {
        // Valider les données entrantes
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // Validation pour le champ media
            'event_date' => 'required|date',
            'churches' => 'required|array', // Validation pour les églises sélectionnées
            'churches.*' => 'exists:church,id', // Vérifiez que l'ID de l'église existe
            'periode_time' => 'required|date', // Validation pour une période unique
        ]);

        // Trouver la cérémonie à mettre à jour
        $ceremonie = Ceremonie::findOrFail($id);

        // Gérer le téléchargement du fichier média, si un nouveau fichier est fourni
        $mediaPath = $ceremonie->media; // Gardez l'ancien chemin par défaut
        if ($request->hasFile('media')) {
            // Supprimer l'ancien fichier média si nécessaire
            if ($ceremonie->media) {
                Storage::disk('public')->delete($ceremonie->media);
            }

            // Stocker le nouveau fichier
            $mediaFile = $request->file('media');
            $mediaPath = $mediaFile->store('media', 'public'); // 'media' est le dossier et 'public' est le disque
        }

        // Mettre à jour la cérémonie avec les données validées
        $ceremonie->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'media' => $mediaPath, // Enregistrer le chemin du fichier média
            'event_date' => $validatedData['event_date'],
        ]);

        // Préparer les données pour la table pivot avec une période unique
        $syncData = [];
        foreach ($request->churches as $church_id) {
            // Associer chaque église avec la même période
            $syncData[$church_id] = ['periode_time' => $validatedData['periode_time']];
        }

        // Associer les églises et la période à la cérémonie
        $ceremonie->churches()->sync($syncData);

        return redirect()->route('ceremonies.index')->with('success', 'Cérémonie mise à jour avec succès.');
    }






    public function destroy($id)
    {
        $ceremonie = Ceremonie::findOrFail($id);
        $ceremonie->delete();

        return redirect()->route('ceremonies.index')->with('success', 'Cérémonie supprimée avec succès.');
    }
}
