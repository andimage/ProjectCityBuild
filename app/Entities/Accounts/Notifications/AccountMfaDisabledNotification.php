<?php

namespace App\Entities\Accounts\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountMfaDisabledNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('2FA Has Been Disabled on your Account')
            ->greeting('2FA Disabled')
            ->line('Your account is no longer protected by 2-factor authentication.')
            ->line('If this wasn\'t you, please speak to a member of PCB staff immediately.');
    }

    /**
     * Get the array representation of the notification.
     *
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [

        ];
    }
}
