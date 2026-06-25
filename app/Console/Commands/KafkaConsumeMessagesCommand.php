<?php

namespace App\Console\Commands;

use App\Services\DeliveryStatusService;
use App\Services\MessageSenderService;
use Illuminate\Console\Command;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Facades\Kafka;
use Throwable;

class KafkaConsumeMessagesCommand extends Command
{
    protected $signature = 'kafka:consume-messages';

    public function handle(MessageSenderService $sender, DeliveryStatusService $deliveryStatusService): int {
        Kafka::consumer([config('kafka.topics.notifications')])
            ->withHandler(function (ConsumerMessage $message) use ($sender, $deliveryStatusService) {
                $body = $message->getBody();

                $this->line('Body: ' . json_encode($body));

                $messageId = $body['message_id'] ?? null;

                if (! $messageId) {
                    return;
                }

                try {
                    $isSent = $sender->send((int) $messageId);

                    if (! $isSent) {
                        return;
                    }

                    $deliveryStatusService->markDelivered((int) $messageId);
                } catch (Throwable $exception) {
                    $deliveryStatusService->markDropped(
                        (int) $messageId,
                        $exception->getMessage()
                    );
                }
            })
            ->build()
            ->consume();

        return self::SUCCESS;
    }
}
