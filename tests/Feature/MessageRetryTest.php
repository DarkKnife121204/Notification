<?php

namespace Tests\Feature;

use App\Enums\DeliveryChannel;
use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Batch;
use App\Models\Message;
use App\Models\Recipient;
use App\Services\DeliveryStatusService;
use App\Services\MessageSenderService;
use App\Services\Providers\EmailProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Throwable;
use Tests\TestCase;

class MessageRetryTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_is_dropped_after_three_failed_attempts(): void
    {
        $recipient = Recipient::factory()->create();

        $batch = Batch::create([
            'channel' => DeliveryChannel::EMAIL,
            'priority' => Priority::MARKETING,
            'message' => 'Test message',
            'idempotency_key' => 'test-key',
            'request_hash' => 'test-hash',
        ]);

        $message = Message::create([
            'batch_id' => $batch->id,
            'recipient_id' => $recipient->id,
            'channel' => DeliveryChannel::EMAIL,
            'priority' => Priority::MARKETING,
            'status' => Status::QUEUED,
            'message' => 'Test message',
        ]);

        $provider = Mockery::mock(EmailProvider::class);

        $provider->shouldReceive('send')
            ->times(3)
            ->andReturnFalse();

        $this->app->instance(EmailProvider::class, $provider);

        $sender = app(MessageSenderService::class);
        $statusService = app(DeliveryStatusService::class);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $sender->send($message->id);
            } catch (Throwable $exception) {
                $statusService->markFailedOrDropped(
                    $message->id,
                    $exception->getMessage()
                );
            }
        }

        $message->refresh();

        $this->assertEquals(Status::DROPPED, $message->status);
        $this->assertEquals(3, $message->attempts);
        $this->assertNotNull($message->dropped_at);
        $this->assertNotNull($message->error_message);
    }
}
