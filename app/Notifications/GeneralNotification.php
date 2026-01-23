<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    // 1. Tambahkan properti untuk menampung data
    protected $details;

    /**
     * 2. Terima data dari Controller lewat constructor
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * 3. UBAH 'mail' menjadi 'database'
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * 4. Isi toArray agar data masuk ke kolom 'data' di tabel notifications
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->details['title'] ?? 'Notifikasi Baru',
            'message' => $this->details['message'] ?? '',
            'icon'    => $this->details['icon'] ?? '🔔',
            'color'   => $this->details['color'] ?? 'bg-teal-100 text-teal-600',
            'url'     => $this->details['url'] ?? '#',
        ];
    }
}
