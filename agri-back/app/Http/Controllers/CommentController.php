<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Récupérer les commentaires avec filtres optionnels.
     */
    public function index(Request $request)
    {
        $user_id = $request->query('user_id');
        $post_id = $request->query('post_id');
    
        $comments = Comment::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', $user_id);
        })
        ->when($post_id, function ($query, $post_id) {
            return $query->where('post_id', $post_id);
        })
        ->with('user:id,name') // Include user name
        ->get();
    
        return response()->json($comments);
    }
    


    /**
     * Ajouter un nouveau commentaire.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'user_id' => 'required|integer|exists:users,id', // Assurez-vous que l'utilisateur existe
            'content' => 'required|string|max:500',
        ]);

        // Créer un commentaire
        $comment = Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => $validated['user_id'],
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Commentaire ajouté avec succès !',
            'comment' => $comment,
        ], 201);
    }
}
