<?php

namespace App\Http\Controllers\Api;

use App\Models\SujetDeDiscussion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SujetDeDiscussionApiController extends Controller
{


    public function index()
    {
        // Récupérer l'utilisateur connecté
        $fidele = auth()->user()->fidele;
        $chantre = auth()->user()->chantre;
        $serviteur_de_dieu = auth()->user()->serviteur_de_dieu;

        // Si l'utilisateur n'est ni un Fidele ni un Chantre
        if (!$fidele && !$chantre && !$serviteur_de_dieu) {
            return response()->json([
                'error' => 'Vous n\'êtes ni un Fidele ni un Chantre.',
            ], 403); // 403 Forbidden
        }

        // Récupérer tous les sujets de discussion dont la date est aujourd'hui
        $sujets = SujetDeDiscussion::whereDate('date', today())->get();

        // Retourner la réponse JSON avec les sujets
        return response()->json([
            'success' => true,
            'data' => $sujets,
        ], 200); // 200 OK
    }




    // public function store(Request $request)
    // {
    //     // Valider les données
    //     $validatedData = $request->validate([
    //         'theme' => 'required|string|max:255',
    //         'date' => 'required|date',
    //         'body' => 'required|string',
    //     ]);

    //     // Créer un nouveau sujet de discussion en associant l'utilisateur connecté
    //     $sujet = SujetDeDiscussion::create([
    //         'theme' => $validatedData['theme'],
    //         'date' => $validatedData['date'],
    //         'body' => $validatedData['body'],
    //         'administrateur_id' => auth()->id(), // Récupération de l'ID de l'utilisateur connecté
    //     ]);

    //     // Retourner une réponse JSON avec les détails du sujet créé
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Sujet de discussion créé avec succès.',
    //         'data' => $sujet,
    //     ], 201); // 201 Created
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
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être un Fidele, un Chantre ou un Serviteur de Dieu pour ajouter un commentaire.',
            ], 403);
        }

        // Retourner la réponse après avoir ajouté le commentaire
        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté avec succès.',
            'data' => $sujet, // Optionally, include the updated subject data
        ]);
    }




    // Afficher un sujet de discussion spécifique
    public function user_show_discussion(SujetDeDiscussion $sujet)
    {
        $user = auth()->user(); // Get the currently authenticated user

        // Check the user's roles
        $fidele = $user->fidele;
        $chantre = $user->chantre;
        $serviteur_de_dieu = $user->serviteur_de_dieu;

        // If the user is neither a Fidele, Chantre, nor ServiteurDeDieu
        if (!$fidele && !$chantre && !$serviteur_de_dieu) {
            return response()->json([
                'success' => false,
                'message' => 'You must be a Fidele, Chantre, or Serviteur de Dieu to view this discussion.',
            ], 403);
        }

        // Return the data for the specific discussion subject
        return response()->json([
            'success' => true,
            'data' => $sujet,
        ]);
    }
}
