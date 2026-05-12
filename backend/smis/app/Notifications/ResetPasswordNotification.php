<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:4200');
        $email = urlencode($notifiable->getEmailForPasswordReset());
        
        $url = $frontendUrl . '/reset-password?token=' . $this->token . '&email=' . $email;
        $resendUrl = $frontendUrl . '/forgot-password?auto_resend=true&email=' . $email;

        return (new MailMessage)
            ->subject('SMIS - Reset Your Password')
            ->view('emails.reset_password', [
                'url' => $url,
                'resendUrl' => $resendUrl,
                'name' => $notifiable->first_name,
            ]);
    }
}
