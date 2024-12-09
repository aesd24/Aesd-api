<?php

namespace App\Http\Controllers;

use App\Models\SujetDeDiscussion;

use App\Models\Fidele;
use App\Models\Chantre;
use Illuminate\Http\Request;

class SujetDeDiscussionController extends Controller
{
    // Afficher la liste des sujets de discussion
    // public function index()
    // {
    //     $sujets = SujetDeDiscussion::with(['fideles', 'chantres'])->get(); // Récupérer tous les sujets avec fidèles et chantres
    //     return view('sujets.index', compact('sujets')); // Retourner la vue avec les sujets
    // }




    public function index()
    {
        $sujets = SujetDeDiscussion::all(); // Récupérer tous les sujets avec fidèles et chantres
        return view('sujets.index', compact('sujets')); // Retourner la vue avec les sujets
    }



    // public function user_discussion()
    // {
    //     // Récupérer l'utilisateur connecté
    //     // $user = auth()->user();

    //     // // Vérification si l'utilisateur est un Fidele
    //     // $fidele = Fidele::where('user_id', $user->id)->first();

    //     // // Vérification si l'utilisateur est un Chantre
    //     // $chantre = Chantre::where('user_id', $user->id)->first();


    //     $fidele =  auth()->user()->fidele;
    //     $chantre = auth()->user()->chantre;

    //     // Si l'utilisateur n'est ni un Fidele ni un Chantre
    //     if (!$fidele && !$chantre) {
    //         // Redirige vers la page précédente avec un message d'erreur
    //         return back()->with('error', 'Vous n\'êtes ni un Fidele ni un Chantre.');
    //     }

    //     // Récupérer tous les sujets de discussion
    //     $sujets = SujetDeDiscussion::all();

    //     // Retourner la vue avec les sujets
    //     return view('sujets.user_discussion', compact('sujets'));
    // }






    public function user_discussion()
    {
        // Récupérer l'utilisateur connecté
        $fidele = auth()->user()->fidele;
        $chantre = auth()->user()->chantre;

        $serviteur_de_dieu = auth()->user()->serviteur_de_dieu;

        // Si l'utilisateur n'est ni un Fidele ni un Chantre
        if (!$fidele && !$chantre && !$serviteur_de_dieu) {
            // Redirige vers la page précédente avec un message d'erreur
            return back()->with('error', 'Vous n\'êtes ni un Fidele ni un Chantre.');
        }



        // Récupérer tous les sujets de discussion dont la date est aujourd'hui
        $sujets = SujetDeDiscussion::whereDate('date', today())->get();

        // Retourner la vue avec les sujets
        return view('sujets.user_discussion', compact('sujets'));
    }



    // Afficher le formulaire de création d'un sujet de discussion
    public function create()
    {
        return view('sujets.create'); // Retourner la vue de création
    }

    // Stocker un nouveau sujet de discussion
    // public function store(Request $request)
    // {
    //     dd($request);
    //     // Valider les données
    //     $request->validate([
    //         'theme' => 'required|string|max:255',
    //         'date' => 'required|date',
    //         'body' => 'required|string',
    //     ]);

    //     // Créer un nouveau sujet de discussion
    //     SujetDeDiscussion::create($request->all());

    //     return redirect()->route('sujets.index')->with('success', 'Sujet de discussion créé avec succès.');
    // }





    public function store(Request $request)
    {

        // Valider les données
        $request->validate([
            'theme' => 'required|string|max:255',
            'date' => 'required|date',
            'body' => 'required|string',
        ]);

        // Créer un nouveau sujet de discussion en associant l'utilisateur connecté
        SujetDeDiscussion::create([
            'theme' => $request->theme,
            'date' => $request->date,
            'body' => $request->body,
            'administrateur_id' => auth()->id(), // Récupération de l'ID de l'utilisateur connecté
        ]);

        return redirect()->route('sujets.index')->with('success', 'Sujet de discussion créé avec succès.');
    }





    // Méthode pour ajouter un commentaire
    // public function addComment(Request $request, SujetDeDiscussion $sujet)
    // {
    //     // Validation des données
    //     $request->validate([
    //         'comment' => 'required|string|max:1000', // Validation du champ commentaire
    //     ]);


    //     // dd($sujet->id);


    //     // // Si l'utilisateur est un fidèle, ajouter son commentaire
    //     // if ($user->fidele) {
    //     //     $user->fidele->addCommentToSujet($sujet, $request->comment);
    //     // }

    //     // // Si l'utilisateur est un chantre, ajouter son commentaire
    //     // if ($user->chantre) {
    //     //     $user->chantre->addCommentToSujet($sujet, $request->comment);
    //     // }


    //     // // Si l'utilisateur est un serviteur_de_dieu, ajouter son commentaire
    //     // if ($user->serviteur_de_dieu) {
    //     //     $user->serviteur_de_dieu->addCommentToSujet($sujet, $request->comment);
    //     // }


    //     $user = auth()->user(); // Récupérer l'utilisateur connecté
    //     // Récupérer l'utilisateur connecté
    //     $fidele = auth()->user()->fidele;
    //     $chantre = auth()->user()->chantre;
    //     $serviteur_de_dieu = auth()->user()->serviteur_de_dieu;



    //     if ($fidele) {
    //         $user->fidele->addCommentToSujet($sujet, $request->comment);
    //     } elseif ($chantre) {
    //         $user->chantre->addCommentToSujet($sujet, $request->comment);
    //     } elseif ($serviteur_de_dieu) {
    //         $user->$serviteur_de_dieu->addCommentToSujet($sujet, $request->comment);
    //     } else {
    //         return back()->with('error', 'Vous n\'êtes ni un Fidele ni un Chantre ni un serviteur de Dieu.');
    //     }

    //     // Rediriger avec un message de succès
    //     return redirect()->route('sujets.user_show_discussion', $sujet)->with('success', 'Commentaire ajouté avec succès.');
    // }







    public function addComment(Request $request, SujetDeDiscussion $sujet)
    {
        // Validation des données
        $request->validate([
            'comment' => 'required|string|max:1000', // Validation du champ commentaire
        ]);

        $user = auth()->user(); // Récupérer l'utilisateur connecté

        // Vérifier les relations
        $fidele = $user->fidele;
        $chantre = $user->chantre;
        $serviteur_de_dieu = $user->serviteur_de_dieu;

        // Ajouter un commentaire en fonction du rôle de l'utilisateur
        if ($fidele) {
            $fidele->addCommentToSujet($sujet, $request->comment);
        } elseif ($chantre) {
            $chantre->addCommentToSujet($sujet, $request->comment);
        } elseif ($serviteur_de_dieu) {
            $serviteur_de_dieu->addCommentToSujet($sujet, $request->comment);
        } else {
            // Si l'utilisateur n'est ni Fidele, ni Chantre, ni Serviteur de Dieu
            return back()->with('error', 'Vous devez être un Fidele, un Chantre ou un Serviteur de Dieu pour ajouter un commentaire.');
        }

        // Rediriger avec un message de succès
        return redirect()->route('sujets.user_show_discussion', $sujet)->with('success', 'Commentaire ajouté avec succès.');
    }







    // public function addComment(Request $request, SujetDeDiscussion $sujet)
    // {
    //     // Validation des données
    //     $request->validate([
    //         'comment' => 'required|string|max:1000', // Validation du champ commentaire
    //     ]);

    //     $user = auth()->user(); // Récupérer l'utilisateur connecté

    //     // Si l'utilisateur est un fidèle, attacher le sujet avec le commentaire
    //     if ($user->fidele) {
    //         $user->fidele->sujetsDeDiscussion()->attach($sujet->id, [
    //             'Comment' => $request->comment,
    //         ]);
    //     }

    //     // Si l'utilisateur est un chantre, attacher le sujet avec le commentaire
    //     if ($user->chantre) {
    //         $user->chantre->sujetsDeDiscussion()->attach($sujet->id, [
    //             'Comment' => $request->comment,
    //         ]);
    //     }

    //     // Rediriger avec un message de succès
    //     return redirect()->route('sujets.user_show_discussion', $sujet)->with('success', 'Commentaire ajouté avec succès.');
    // }






    // public function addComment(Request $request, SujetDeDiscussion $sujet)
    // {
    //     // Validation des données
    //     $request->validate([
    //         'comment' => 'required|string|max:1000', // Validation du champ commentaire
    //     ]);

    //     $user = auth()->user(); // Récupérer l'utilisateur connecté

    //     // Si l'utilisateur est un fidèle, ajouter son commentaire
    //     if ($user->fidele) {
    //         $fidele = $user->fidele;
    //         $fidele->sujetsDeDiscussion()->attach($sujet->id, [
    //             'Comment' => $request->comment,
    //             'fidele_id' => $fidele->id, // Enregistrement de l'ID du fidèle
    //         ]);
    //     }

    //     // Si l'utilisateur est un chantre, ajouter son commentaire
    //     if ($user->chantre) {
    //         $chantre = $user->chantre;
    //         $chantre->sujetsDeDiscussion()->attach($sujet->id, [
    //             'Comment' => $request->comment,
    //             'chantre_id' => $chantre->id, // Enregistrement de l'ID du chantre
    //         ]);
    //     }

    //     // Rediriger avec un message de succès
    //     return redirect()->route('sujets.user_show_discussion', $sujet)
    //         ->with('success', 'Commentaire ajouté avec succès.');
    // }








    // Afficher un sujet de discussion spécifique
    public function user_show_discussion(SujetDeDiscussion $sujet)
    {

        $fidele =  auth()->user()->fidele;
        $chantre = auth()->user()->chantre;
        $serviteur_de_dieu = auth()->user()->serviteur_de_dieu;


        // Si l'utilisateur n'est ni un Fidele ni un Chantre
        if (!$fidele && !$chantre  && !$serviteur_de_dieu) {
            // Redirige vers la page précédente avec un message d'erreur
            return back()->with('error', 'Vous n\'êtes ni un Fidele ni un Chantre.');
        }

        return view('sujets.user_show_discussion', compact('sujet')); // Retourner la vue pour afficher le sujet
    }


    public function show(SujetDeDiscussion $sujet)
    {
        return view('sujets.show', compact('sujet')); // Retourner la vue pour afficher le sujet
    }



    // Afficher le formulaire d'édition d'un sujet de discussion
    public function edit(SujetDeDiscussion $sujet)
    {
        return view('sujets.edit', compact('sujet')); // Retourner la vue d'édition
    }




    // Mettre à jour un sujet de discussion
    public function update(Request $request, SujetDeDiscussion $sujet)
    {
        // Valider les données
        $request->validate([
            'theme' => 'required|string|max:255',
            'date' => 'required|date',
            'body' => 'required|string',
        ]);

        // Mettre à jour le sujet de discussion
        $sujet->update($request->all());

        return redirect()->route('sujets.index')->with('success', 'Sujet de discussion mis à jour avec succès.');
    }

    // Supprimer un sujet de discussion
    public function destroy(SujetDeDiscussion $sujet)
    {
        $sujet->delete(); // Supprimer le sujet

        return redirect()->route('sujets.index')->with('success', 'Sujet de discussion supprimé avec succès.');
    }

    
}
