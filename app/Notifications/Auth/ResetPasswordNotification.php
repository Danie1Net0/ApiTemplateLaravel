<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

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
        $token = new HtmlString(<<<EOL
          <div style='text-align: center; font-size: 24px; margin-bottom: 15px;'>
            <strong>$notifiable->token</strong>
          </div>
        EOL);

        return (new MailMessage)
            ->subject('Recuperar Senha')
            ->greeting('Olá!')
            ->line('Seu código de recuperação de senha é:')
            ->line($token)
            ->line('Este código expirará em 60 minutos.');

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
