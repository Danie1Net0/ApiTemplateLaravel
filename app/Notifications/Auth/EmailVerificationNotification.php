<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class EmailVerificationNotification
 * @package App\Notifications\Auth
 */
class EmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = env('APP_URL_FRONT', 'http://localhost:4200/') .
            "auth/completar-cadastro/{$notifiable->id}/{$notifiable->activation_token}";

        return (new MailMessage)
            ->subject('Verificar Cadastro')
            ->greeting('Seja bem vindo!')
            ->line('Clique no botão abaixo para confirmar seu cadastro.')
            ->action('Confirmar Cadastro', $verificationUrl)
            ->line('Obrigado por juntar-se a nós!');

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
