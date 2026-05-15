<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Notifications\Channels\SystemChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return [SystemChannel::class];
    }

    public function toSystem(object $notifiable): array
    {
        $statusText = $this->payment->status === 'verified' ? 'telah diverifikasi' : 'ditolak';
        $paymentType = strtoupper($this->payment->payment_type);
        $rejectionReason = $this->payment->rejection_reason ? ". " . $this->payment->rejection_reason : '';

        return [
            'title' => "Status Pembayaran {$paymentType} {$statusText}",
            'message' => "Pembayaran Anda {$statusText}{$rejectionReason}",
            'type' => 'payment',
            'reference_type' => Payment::class,
            'reference_id' => $this->payment->id,
        ];
    }
}
