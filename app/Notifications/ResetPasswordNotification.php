<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends \Illuminate\Auth\Notifications\ResetPassword
{
    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(trans('passwords.mail_subject'))
            ->line(trans('passwords.mail_line_1'))
            ->action(trans('passwords.reset_password'), url(config('app.url').route('password.reset', [$this->token], false)))
            ->line(trans('passwords.mail_line_2'));
    }
}
