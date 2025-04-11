<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;  // Importation manquante

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Fetch all users
    // In UserController.php
    // In UserController.php
    public function index()
    {
        // Remove role check for debugging purposes
        $users = User::all();
        return response()->json($users);
    }
    



    // Delete a user by ID
    public function destroy($id)
{
    $user = User::find($id);

    if ($user) {
        $user->delete();  // Delete the user
        return response()->json(['message' => 'User deleted successfully']);
    }

    return response()->json(['error' => 'User not found'], 404);
}
 // Update the VIP status of a user
 public function updateVipStatus(Request $request, $user_id)
 {
     $user = User::find($user_id);

     if (!$user) {
         return response()->json(['message' => 'Utilisateur non trouvé'], 404);
     }

     // Mettre à jour le vip_status
     $user->vip_status = $request->vip_status;
     $user->save();

     return response()->json(['message' => 'Statut VIP mis à jour', 'user' => $user]);
 }



}