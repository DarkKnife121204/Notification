<?php

namespace App\Services\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\Mail;

class EmailProvider
{
    public function send(Message $message): bool
    {
        Mail::raw($message->message, function ($mail) use ($message) {
            $mail->to($message->recipient->email)
                ->subject('Notification');
        });

        return true;
    }
}
