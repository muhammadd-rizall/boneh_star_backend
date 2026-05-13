<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.frontend_url') . '/auth/reset-password?token=' . $this->token . '&email=' . $notifiable->getEmailForPasswordReset();

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi - Boneh Star')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Boneh Star Anda.')
            ->line('Silakan klik tombol di bawah ini untuk melanjutkan proses pengaturan ulang kata sandi.')
            ->action('Atur Ulang Password', $url)
            ->line('Tautan ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.')
            ->salutation('Salam hangat,' . "\n" . 'Team Boneh Star');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
