<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Message;

class DeliveryStatusService
{
    private const MAX_ATTEMPTS = 3;

    public function markDelivered(int $messageId): void
    {
        $message = Message::find($messageId);

        if (! $message) {
            return;
        }

        $message->update([
            'status' => Status::DELIVERED,
            'delivered_at' => now(),
            'error_message' => null,
        ]);
    }

    public function markFailedOrDropped(int $messageId, string $reason): bool
    {
        $message = Message::find($messageId);

        if (!$message) {
            return true;
        }

        if ($message->attempts >= self::MAX_ATTEMPTS) {
            $message->update([
                'status' => Status::DROPPED,
                'dropped_at' => now(),
                'error_message' => $reason,
            ]);

            return true;
        }

        $message->update([
            'status' => Status::QUEUED,
            'error_message' => $reason,
        ]);

        return false;
    }
}
