<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Message;

class DeliveryStatusService
{
    public function markDelivered(int $messageId): void
    {
        $message = Message::find($messageId);

        if (! $message) {
            return;
        }

        if ($message->status !== Status::SENT) {
            return;
        }

        $message->update([
            'status' => Status::DELIVERED,
            'delivered_at' => now(),
            'error_message' => null,
        ]);
    }

    public function markDropped(int $messageId, string $reason): void
    {
        $message = Message::find($messageId);

        if (! $message) {
            return;
        }

        if (in_array($message->status, [Status::DELIVERED, Status::DROPPED], true)) {
            return;
        }

        $message->update([
            'status' => Status::DROPPED,
            'dropped_at' => now(),
            'error_message' => $reason,
        ]);
    }
}
