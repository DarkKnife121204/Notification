<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Services\DeliveryStatusService;
use App\Services\MessageSenderService;
use App\Services\RetryService;
use Illuminate\Console\Command;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Facades\Kafka;
use Throwable;

class KafkaConsumeMessagesCommand extends Command
{
    protected $signature = 'kafka:consume-messages';

    public function handle(MessageSenderService $sender, DeliveryStatusService $deliveryStatusService, RetryService $retryService): int
    {
        $topic = config('kafka.consumer_topic');

        Kafka::consumer([$topic])
            ->withHandler(function (ConsumerMessage $message) use ($sender, $deliveryStatusService, $topic, $retryService) {
                $body = $message->getBody();

                $this->line('Topic: ' . $topic);
                $this->line('Body: ' . json_encode($body));

                $messageId = $body['message_id'] ?? null;

                if (!$messageId) {
                    return;
                }

                try {
                    $isSent = $sender->send((int) $messageId);

                    if (!$isSent) {
                        return;
                    }

                    $deliveryStatusService->markDelivered((int) $messageId);
                } catch (Throwable $exception) {
                    $isDropped = $deliveryStatusService->markFailedOrDropped(
                        (int) $messageId,
                        $exception->getMessage()
                    );

                    if (!$isDropped) {
                        $message = Message::find((int) $messageId);

                        $this->error('Scheduling retry: ' . $message->id);

                        $retryService->schedule(
                            $message->id,
                            $topic,
                            $message->attempts,
                        );
                    }
                }
            })
            ->build()
            ->consume();

        return self::SUCCESS;
    }
}
