<?php

namespace App\Jobs;

use App\Enums\DeliveryChannel;
use App\Enums\Status;
use App\Models\Message;
use App\Services\Providers\EmailProvider;
use App\Services\Providers\SmsProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;
use Throwable;

class SendMessageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(private readonly int $messageId)
    {}

    public function handle(): void
    {
        $message = Message::with('recipient')->find($this->messageId);

        if (!$message) {
            return;
        }

        if (in_array($message->status, [Status::DELIVERED, Status::DROPPED], true)) {
            return;
        }

        $message->update([
            'attempts' => $message->attempts + 1,
        ]);

        $isSent = match ($message->channel) {
            DeliveryChannel::SMS => app(SmsProvider::class)->send($message),
            DeliveryChannel::EMAIL => app(EmailProvider::class)->send($message),
        };

        if (!$isSent) {
            throw new RuntimeException('Нету подходящего провайдера');
        }

        $message->update([
            'status' => Status::SENT,
            'sent_at' => now(),
        ]);

        $message->update([
            'status' => Status::DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $message = Message::find($this->messageId);

        if (!$message) {
            return;
        }

        $message->update([
            'status' => Status::DROPPED,
            'dropped_at' => now(),
            'error_message' => $exception->getMessage(),
        ]);
    }
}
