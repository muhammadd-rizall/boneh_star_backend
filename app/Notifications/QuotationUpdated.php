<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Quotation;
use App\Notifications\Channels\SystemChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuotationUpdated extends Notification
{
    use Queueable;

    public function __construct(public Quotation $quotation, public string $action = 'updated')
    {
    }

    public function via(object $notifiable): array
    {
        return [SystemChannel::class];
    }

    public function toSystem(object $notifiable): array
    {
        $title = $this->action === 'new' ? 'Penawaran Harga Baru' : 'Pembaruan Penawaran Harga';
        $message = $this->action === 'new' 
            ? 'Anda telah menerima penawaran harga baru untuk pemesanan Anda. Silakan tinjau kembali.'
            : 'Admin telah memperbarui penawaran harga untuk pemesanan Anda. Silakan tinjau kembali.';

        return [
            'title' => $title,
            'message' => $message,
            'type' => 'quotation',
            'reference_type' => Booking::class,
            'reference_id' => $this->quotation->booking_id,
        ];
    }
}
