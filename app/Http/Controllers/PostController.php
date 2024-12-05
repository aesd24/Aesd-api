<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // Afficher la liste des posts
    public function index()
    {
        $posts = Post::with('servant')->get(); // Récupérer tous les posts avec leurs serviteurs
        return view('posts.index', compact('posts')); // Retourner la vue avec les posts
    }

    // Afficher le formulaire de création d'un post
    public function create()
    {
        return view('posts.create'); // Retourner la vue de création
    }

    // Stocker un nouveau post
    public function store(Request $request)
    {
        // Valider les données
        $request->validate([
            'contenu' => 'required|string',
            'image' => 'nullable|image|max:2048', // Validation d'image
            'published_at' => 'nullable|date',
            'servant_id' => 'required|exists:serviteurs_de_dieu,id', // Assurez-vous que le servant existe
        ]);

        // Gérer l'image si elle est présente
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/posts', 'public'); // Enregistrer l'image
        }

        // Créer un nouveau post
        Post::create([
            'contenu' => $request->contenu,
            'image' => $imagePath,
            'published_at' => $request->published_at,
            'servant_id' => $request->servant_id,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post créé avec succès.');
    }

    // Afficher un post spécifique
    public function show(Post $post)
    {
        return view('posts.show', compact('post')); // Retourner la vue pour afficher le post
    }

    // Afficher le formulaire d'édition d'un post
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post')); // Retourner la vue d'édition
    }

    // Mettre à jour un post
    public function update(Request $request, Post $post)
    {
        // Valider les données
        $request->validate([
            'contenu' => 'required|string',
            'image' => 'nullable|image|max:2048', // Validation d'image
            'published_at' => 'nullable|date',
            'servant_id' => 'required|exists:serviteurs_de_dieu,id', // Assurez-vous que le servant existe
        ]);

        // Gérer l'image si elle est présente
        $imagePath = $post->image; // Garder l'ancienne image par défaut
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            // Enregistrer la nouvelle image
            $imagePath = $request->file('image')->store('images/posts', 'public');
        }

        // Mettre à jour le post
        $post->update([
            'contenu' => $request->contenu,
            'image' => $imagePath,
            'published_at' => $request->published_at,
            'servant_id' => $request->servant_id,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post mis à jour avec succès.');
    }

    // Supprimer un post
    public function destroy(Post $post)
    {
        // Supprimer l'image du disque si elle existe
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete(); // Supprimer le post

        return redirect()->route('posts.index')->with('success', 'Post supprimé avec succès.');
    }
}
