<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BatchVerifiedNotification extends Notification
{
    use Queueable;

    public $farmer;

    /**
     * Create a new notification instance.
     *
     * @param $farmer
     */
    public function __construct($farmer)
    {
        $this->farmer = $farmer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Send via email and store in the database
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
            ->subject('Congratulations! You Are Our Best Seller')
            ->line("Dear {$this->farmer->name},")
            ->line("We are pleased to inform you that you have been recognized as the best seller.")
            ->line("Your hard work and dedication have paid off, and we want to congratulate you on your achievement!")
            ->action('View Your Products', url('/farmer/products'))
            ->line('Thank you for being an important part of our community. Keep up the great work!');
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
            'farmer_name' => $this->farmer->name,
            'message' => 'You have been recognized as the best seller!',
        ];
    }
}
