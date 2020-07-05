<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class ResetPasswordNotification
 * @package App\Notifications\Auth
 */
class ResetPasswordNotification extends Notification
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
        $passwordResetUrl = env('APP_URL_FRONT', 'http://localhost:4200/') .
            "auth/nova-senha/{$notifiable->user_id}/{$notifiable->token}";

        return (new MailMessage)
            ->subject('Recuperar Senha')
            ->greeting('Olá!')
            ->line('Clique no botão abaixo para redefinir sua senha.')
            ->action('Redefinir Senha', $passwordResetUrl)
            ->line('Esse link de redefinição de senha expirará em 60 minutos.');

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
