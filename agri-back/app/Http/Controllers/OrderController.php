<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BatchVerifiedNotification;

use App\Notifications\OrderValidatedNotification;


class OrderController extends Controller
{
    /**
     * Récupérer les commandes avec filtres optionnels.
     */
    public function index(Request $request)
    {
        $user_id = $request->query('user_id');  // ID de l'utilisateur (client)
        $product_id = $request->query('product_id');  // ID du produit (pour fermiers)
    
        // Filtrage des commandes
        $orders = Order::when($user_id, function ($query, $user_id) {
                return $query->where('user_id', $user_id);  // Si un user_id est passé, on filtre sur celui-ci
            })
            ->when($product_id, function ($query, $product_id) {
                return $query->whereHas('orderItems', function ($query) use ($product_id) {
                    $query->where('product_id', $product_id);  // Si un product_id est passé, on filtre sur celui-ci
                });
            })
            ->with('orderItems.product', 'user')  // Include the user (client) relationship
            ->get();
    
        return response()->json($orders);
    }
    

public function validateOrder($order_id)
{
    // Retrieve the order
    $order = Order::find($order_id);
    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    // Check if the order is already validated
    if ($order->status == 'completed') {
        return response()->json(['message' => 'Order is already validated'], 400);
    }

    // Update the order status to 'completed'
    $order->status = 'completed';
    $order->save();
    $user = $order->user; // Récupérer l'utilisateur associé à la commande
    if ($user) {
        $user->notify(new OrderValidatedNotification($order)); // Envoyer la notification à l'utilisateur
    }

    // Update the sales and revenue for each product in the order
    foreach ($order->orderItems as $orderItem) {
        $product = $orderItem->product;

        // Increment sales by quantity ordered
        $product->sales += $orderItem->quantity;

        // Update the revenue (price * quantity)
        $product->revenue += $product->price * $orderItem->quantity;
        // Decrement stock by quantity sold
        $product->stock -= $orderItem->quantity;

        // Save the updated product data
        $product->save();
    }

    // Return the updated order with the status changed
    return response()->json($order);
}
public function getOrderNotificationsForClient($client_id)
{
    // Vérifier si le client existe
    $client = User::find($client_id);
    if (!$client) {
        return response()->json(['message' => 'Client non trouvé'], 404);
    }

    // Récupérer toutes les notifications non lues du client
    $notifications = $client->unreadNotifications->where('type', OrderValidatedNotification::class);

    // Si vous voulez récupérer toutes les notifications, lues et non lues, décommentez la ligne suivante
    // $notifications = $client->notifications()->where('type', OrderPlacedNotification::class)->get();

    return response()->json($notifications);
}

public function getFarmerStatistics()
{
    // Aggregate revenue and sales for each farmer, only for completed orders
    $stats = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('users', 'products.farmer_id', '=', 'users.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id') // Ensure only completed orders
        ->where('orders.status', 'completed') // Include only completed orders
        ->select(
            'users.id as farmer_id',
            'users.name as farmer_name',
            DB::raw('SUM(order_items.quantity) as total_sales'),
            DB::raw('SUM(order_items.quantity * products.price) as total_revenue')
        )
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total_revenue') // Order by revenue from highest to lowest
        ->get();

    return response()->json($stats);
}





/*methode for the batch 

public function getFarmerStatistics()
{
    // Aggregate revenue and sales for each farmer
    $stats = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('users', 'products.farmer_id', '=', 'users.id')
        ->select(
            'users.id as farmer_id',
            'users.name as farmer_name',
            DB::raw('SUM(order_items.quantity) as total_sales'),
            DB::raw('SUM(order_items.quantity * products.price) as total_revenue') // Use product price for revenue
        )
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total_sales') // Order by sales from highest to lowest
        ->get();

    // Get the user (farmer) with the highest sales
    $bestSeller = $stats->first();

    // If the best seller exists, send a notification
    if ($bestSeller) {
        // Retrieve the user with the highest sales
        $user = User::find($bestSeller->farmer_id);

        // Send notification to the user using notify()
        if ($user) {
            $user->notify(new BatchVerifiedNotification($user)); // Using notify() and email only
        }
    }

    // Return the statistics
    return response()->json($stats);
}

*/



}