<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Envoyer via email et sauvegarder dans la base de données
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Nouvel utilisateur créé')
            ->line("Un nouveau compte utilisateur a été créé.")
            ->line("Nom : {$this->user->name}")
            ->line("Email : {$this->user->email}")
            ->line("Rôle : {$this->user->role}")
            ->action('Voir les détails', url('/admin/users'))
            ->line('Merci de vérifier ce nouvel utilisateur.');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'role' => $this->user->role,
        ];
    }
}
