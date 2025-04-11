<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserCreatedNotification;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:4|confirmed',
        'role' => 'required|in:admin,farmer,client',
    ]);

    $user = User::create($validatedData);

    // Notifier l'admin
    $admin = User::where('role', 'admin')->first(); // Assurez-vous qu'un admin existe
    if ($admin) {
        $admin->notify(new UserCreatedNotification($user));
    }

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user,
    ], 201);
}


   


public function login(Request $request)
{
    // Validate the incoming data
    $validatedData = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:4',
    ]);

    // Find the user by email
    $user = User::where('email', $validatedData['email'])->first();

    if (!$user) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // Check if the hashed password matches
    if (Hash::check($validatedData['password'], $user->password)) {
        // Store user and role in the session
        session(['user' => $user, 'role' => $user->role]);

        // Return the user info (including id and name) and role
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'role' => $user->role,
            'message' => 'Login successful',
        ]);
    }

    // Password did not match
    return response()->json(['error' => 'Invalid credentials'], 401);
}


public function getNotifications($userId)
{
    // Récupérer l'utilisateur par son ID
    $user = User::find($userId);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Récupérer les notifications non lues
    $unreadNotifications = $user->unreadNotifications;

    return response()->json([
        'unread' => $unreadNotifications,
    ]);
}




    
}
