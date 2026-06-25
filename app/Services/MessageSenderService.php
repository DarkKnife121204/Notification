<?php

namespace App\Services;

use App\Enums\DeliveryChannel;
use App\Enums\Status;
use App\Models\Message;
use App\Services\Providers\EmailProvider;
use App\Services\Providers\SmsProvider;
use RuntimeException;

class MessageSenderService
{
    public function send(int $messageId): bool
    {
        $message = Message::with('recipient')->find($messageId);

        if (! $message) {
            return false;
        }

        if (in_array($message->status, [Status::DELIVERED, Status::DROPPED], true)) {
            return false;
        }

        $message->update([
            'attempts' => $message->attempts + 1,
            'error_message' => null,
        ]);

        $isSent = match ($message->channel) {
            DeliveryChannel::SMS => app(SmsProvider::class)->send($message),
            DeliveryChannel::EMAIL => app(EmailProvider::class)->send($message),
        };

        if (! $isSent) {
            throw new RuntimeException('Провайдер не принял сообщение');
        }

        $message->update([
            'status' => Status::SENT,
            'sent_at' => now(),
        ]);

        return true;
    }
}
