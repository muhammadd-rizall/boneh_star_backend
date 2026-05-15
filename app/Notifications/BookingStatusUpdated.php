<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Notifications\Channels\SystemChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SystemChannel::class];
    }

    /**
     * Get the system representation of the notification.
     */
    public function toSystem(object $notifiable): array
    {
        $statusText = match ($this->booking->status) {
            'confirmed' => 'telah dikonfirmasi',
            'ongoing' => 'sedang berlangsung',
            'completed' => 'telah selesai',
            'cancelled' => 'telah dibatalkan',
            'quoted' => 'mendapatkan penawaran harga',
            default => 'telah diperbarui',
        };

        return [
            'title' => 'Status Pemesanan Diperbarui',
            'message' => "Status pemesanan #{$this->booking->booking_code} Anda {$statusText}.",
            'type' => 'booking',
            'reference_type' => Booking::class,
            'reference_id' => $this->booking->id,
        ];
    }
}
