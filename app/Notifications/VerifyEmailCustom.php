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
                ->subject('Bienvenue sur Nourriture des Fidèles')
                ->view('emails.verify', [
                    'user' => $notifiable,
                    'actionUrl' => $verificationUrl,
                ]);
        }
        
}
