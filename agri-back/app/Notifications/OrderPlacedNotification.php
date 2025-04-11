<?php

// app/Notifications/OrderPlacedNotification.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedNotification extends Notification
{
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */

    public function via($notifiable)
    {
        return ['mail','database'];  // Vous pouvez aussi envoyer par database ou autres moyens
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Nouvelle Commande')
                    ->greeting('Bonjour ' . $notifiable->name)
                    ->line('Une nouvelle commande a été passée pour l\'un de vos produits.')
                    ->line('Détails de la commande :')
                    ->line('Client : ' . $this->order->user->name)
                    ->line('Total : ' . $this->order->total_amount . '€')
                    ->action('Voir la commande', url('/orders/' . $this->order->id))
                    ->line('Merci d\'avoir utilisé notre service!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'user_name' => $this->order->user->name,
            'total_amount' => $this->order->total_amount,
        ];
    }
}
