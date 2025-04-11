<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;

use App\Models\OrderItem;

use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Récupérer les articles du panier avec des filtres optionnels.
     */
    public function index(Request $request)
    {
        $user_id = $request->query('user_id');  // Récupérer l'ID de l'utilisateur via les paramètres de la requête
        $product_id = $request->query('product_id');  // Filtrer par ID de produit, si fourni
        $quantity = $request->query('quantity');  // Filtrer par quantité, si fourni

        // Vérifier que l'utilisateur a un ID valide
        if (!$user_id) {
            return response()->json(['message' => 'Utilisateur non authentifié ou ID non fourni'], 400);
        }

        // Récupérer les éléments du panier de l'utilisateur
        $cartItems = Cart::where('user_id', $user_id)
            ->when($product_id, function ($query, $product_id) {
                return $query->where('product_id', $product_id);  // Filtrer par produit si nécessaire
            })
            ->when($quantity, function ($query, $quantity) {
                return $query->where('quantity', $quantity);  // Filtrer par quantité si nécessaire
            })
            ->with('product')  // Inclure les informations du produit dans la réponse
            ->get();

        return response()->json($cartItems);
    }

    /**
     * Ajouter un article au panier ou mettre à jour sa quantité.
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'user_id' => 'required|integer|exists:users,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::create([
            'product_id' => $validated['product_id'],
            'user_id' => $validated['user_id'],
            'quantity' => $validated['quantity'],
        ]);

        return response()->json(['message' => 'Produit ajouté au panier avec succès', 'cart'=>$cart,],  201);
    }

    public function placeOrder(Request $request)
{
    // Récupérer l'ID de l'utilisateur depuis la requête
    $user_id = $request->input('user_id');

    // Vérifier que l'utilisateur existe et a des éléments dans le panier
    $cartItems = $request->input('cart_items'); // Récupérer les éléments du panier depuis les données envoyées
    if (empty($cartItems)) {
        return response()->json(['message' => 'Panier vide. Impossible de passer la commande.'], 400);
    }

    // Calculer le montant total de la commande
    $totalAmount = 0;
    foreach ($cartItems as $item) {
        $product = Product::find($item['product_id']);
        if ($product) {
            $totalAmount += $product->price * $item['quantity'];
        }
    }
     // Vérifier si la commande est régulière
     $isRegular = $request->input('is_regular', false);
     $regularityType = null;
     $interval = null;
 
     if ($isRegular) {
         $regularityType = $request->input('regularity_type');
         $interval = $request->input('interval');
     }
 

    // Créer la commande
    $order = Order::create([
        'user_id' => $user_id,
        'total_amount' => $totalAmount,
        'status' => 'pending',
        'is_regular' => $isRegular,
        'regularity_type' => $regularityType,
        'interval' => $interval,
    ]);

    // Ajouter les articles du panier dans la commande
    foreach ($cartItems as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
        ]);
    }

    // Supprimer les éléments du panier après la commande
    Cart::where('user_id', $user_id)->delete();

    // Envoi de la notification au fermier
    foreach ($cartItems as $item) {
        $product = Product::find($item['product_id']);
        if ($product && $product->farmer_id) {
            $farmer = User::find($product->farmer_id);
            if ($farmer) {
                $farmer->notify(new OrderPlacedNotification($order));  // Envoyer la notification au fermier
            }
        }
    }

    return response()->json(['message' => 'Commande passée avec succès', 'order' => $order], 201);
}

// Dans CartController.php

public function getOrdersForFarmer($farmer_id)
{
    // Vérifier si le farmer existe
    $farmer = User::find($farmer_id);
    if (!$farmer) {
        return response()->json(['message' => 'Farmer not found'], 404);
    }

    // Récupérer les commandes associées au farmer, avec les informations du client
    $orders = Order::whereHas('orderItems.product', function ($query) use ($farmer_id) {
        $query->where('farmer_id', $farmer_id);  // Assurez-vous que le produit appartient au farmer
    })
    ->with(['orderItems.product', 'user'])  // Charger les articles de commande et le client
    ->get();

    return response()->json($orders);
}

public function getOrderNotificationsForFarmer($farmer_id)
{
    // Vérifier si le fermier existe
    $farmer = User::find($farmer_id);
    if (!$farmer) {
        return response()->json(['message' => 'Fermier non trouvé'], 404);
    }

    // Récupérer toutes les notifications non lues du fermier
    $notifications = $farmer->unreadNotifications->where('type', OrderPlacedNotification::class);

    // Vous pouvez aussi récupérer toutes les notifications si vous souhaitez inclure celles lues
    // $notifications = $farmer->notifications()->where('type', OrderPlacedNotification::class)->get();

    return response()->json($notifications);
}



}
