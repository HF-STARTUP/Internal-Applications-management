<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class userNotification extends Notification
{
    use Queueable;
    protected $password;
    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        $this->password=$password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('RHOPEN LABS vous invite a rejoindre l\'application Labs App')
                    ->line('Votre mot de passe de connexion est:'. $this->password)
                    ->line('Utiliser le lien suivant pour vous connecter a l\'application')
                    ->action('Se connecter maintenant', "http://192.168.50.176:3000")
                    ->line('Merci de faire partir de la team !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
