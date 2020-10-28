<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

/**
 * Class RegistrationConfirmationNotification
 * @package App\Notifications\Auth
 */
class RegistrationConfirmationNotification extends Notification
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
          <div style='text-align: center; font-size: 24px; margin-bottom: 20px;'>
            <strong>$notifiable->confirmation_token</strong>
          </div>
        EOL);

        return (new MailMessage)
            ->subject('Verificar Cadastro')
            ->greeting('Seja bem vindo!')
            ->line('Seu código de confirmação de cadastro é:')
            ->line($token)
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
