<?php

// Dans app/Notifications/OrderValidatedNotification.php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class OrderValidatedNotification extends Notification
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database']; // Vous pouvez aussi ajouter 'mail' si vous souhaitez envoyer par email
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'user_name' => $this->order->user->name,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
        ];
    }
}
