<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailCustom extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(' Bienvenue sur Nourriture des Fidèles')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Merci de vous être inscrit sur Nourriture des Fidèles !")
            ->action(' Confirmer mon email', $verificationUrl)
            ->line("Ce lien est valable pendant 24 heures.")
            ->line("Si vous n’avez pas demandé cette inscription, vous pouvez ignorer ce message.")
            ->salutation(" L’équipe Nourriture des Fidèles\nwww.nourrituredesfideles.com");
    }
}
