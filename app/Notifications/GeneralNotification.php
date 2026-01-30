<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GeneralNotification extends Notification
{
    use Queueable;
    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        //database simpan riwayat, broadcast kirim real-time
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'   => $this->details['title'],
            'message' => $this->details['message'],
            'icon'    => $this->details['icon'],
            'color'   => $this->details['color'],
            'url'     => $this->details['url'],
        ];
    }

    // public function toBroadcast($notifiable)
    // {
    //     return new BroadcastMessage([
    //         'title'   => $this->details['title'],
    //         'message' => $this->details['message'],
    //         'icon'    => $this->details['icon'], // Berisi tag <i> atau class
    //         'color'   => $this->details['color'],
    //         'url'     => $this->details['url'],
    //     ]);
    // }
}
