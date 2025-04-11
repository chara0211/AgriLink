<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;  // Ajouter cette ligne pour importer le modèle User
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
{
    $farmer_id = $request->query('farmer_id');

    $posts = Post::when($farmer_id, function ($query, $farmer_id) {
        return $query->where('farmer_id', $farmer_id);
    })
    ->get();

    // Ajouter le nom de l'utilisateur et la date de création pour chaque post
    $posts->transform(function ($post) {
        // Trouver l'utilisateur qui a créé le post
        $user = User::find($post->farmer_id);
        $post->userName = $user ? $user->name : 'Utilisateur inconnu'; // Récupérer le nom de l'utilisateur
        $post->createdAt = $post->created_at->format('Y-m-d à H:i:s'); // Formater la date de création si nécessaire
        return $post;
    });

    return response()->json($posts);
}


    public function store(Request $request)
    {
        // Validation des données de la publication
        $validated = $request->validate([
            'farmer_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',  // Validation pour l'image
        ]);

        // Si une image est envoyée
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');  // Stocke l'image
        } else {
            $imagePath = null;
        }

        // Créer le post
        $post = Post::create([
            'farmer_id' => $validated['farmer_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image' => $imagePath,  // Stocke le chemin de l'image
        ]);

        // Récupérer le nom de l'utilisateur associé à farmer_id
        $user = User::find($validated['farmer_id']); // Assurez-vous que 'User' est votre modèle d'utilisateur
        $post->userName = $user ? $user->name : 'Utilisateur inconnu';  // Récupérer le nom

        return response()->json([
            'message' => 'Post ajouté avec succès!',
            'post' => $post,
        ], 201);
    }

    public function update(Request $request, $id)
{
    // Validation des données
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => 'nullable|string',  // Attente d'une chaîne d'image en base64
    ]);

    // Trouver le post à mettre à jour
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['message' => 'Post non trouvé'], 404);
    }

    // Si l'image est envoyée en base64
    if (isset($validated['image']) && !empty($validated['image'])) {
        // Décoder la chaîne base64 et la stocker comme un fichier
        $imageData = base64_decode($validated['image']);  // Décoder la chaîne base64
        $imageName = uniqid() . '.png';  // Générer un nom unique pour l'image
        $path = storage_path('app/public/posts/' . $imageName);  // Définir le chemin pour stocker l'image
        file_put_contents($path, $imageData);  // Sauvegarder l'image sur le serveur

        // Mettre à jour le chemin de l'image dans la base de données
        $validated['image'] = 'posts/' . $imageName;  // Corriger le chemin pour utiliser le lien de stockage
    }

    // Mettre à jour les autres champs du post
    $post->title = $validated['title'];
    $post->content = $validated['content'];
    $post->image = isset($validated['image']) ? $validated['image'] : $post->image;  // Conserver l'ancienne image si aucune nouvelle image n'est fournie
    $post->save();

    return response()->json([
        'message' => 'Post mis à jour avec succès!',
        'post' => $post,
    ], 200);
}


public function destroy($id)
{
    $post = Post::find($id);

    if (!$post) {
        return response()->json(['message' => 'Post non trouvé'], 404);
    }

    $post->delete();

    return response()->json(['message' => 'Post supprimé avec succès']);
}

}
