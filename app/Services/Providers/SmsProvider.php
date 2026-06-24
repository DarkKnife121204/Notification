<?php

namespace App\Services\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\Log;

class SmsProvider
{
    public function send(Message $message): bool
    {
        Log::info('SMS sent', [
            'phone' => $message->recipient->phone,
            'message' => $message->message,
        ]);

        return true;
    }
}
