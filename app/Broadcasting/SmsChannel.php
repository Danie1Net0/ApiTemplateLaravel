<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SmsChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        Http::post('https://api.smsdev.com.br/v1/send', [
            'key' => config('services.sms_dev.key'),
            'type' => 9,
            'number' => $message['to'],
            'msg' => $message['message']
        ]);
    }
}
