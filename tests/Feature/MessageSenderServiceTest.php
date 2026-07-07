<?php

namespace Tests\Feature;

use App\Enums\DeliveryChannel;
use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Batch;
use App\Models\Message;
use App\Models\Recipient;
use App\Services\MessageSenderService;
use App\Services\Providers\EmailProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class MessageSenderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_message_is_sent(): void
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
            ->once()
            ->andReturnTrue();

        $this->app->instance(EmailProvider::class, $provider);

        app(MessageSenderService::class)->send($message->id);

        $message->refresh();

        $this->assertEquals(Status::SENT, $message->status);
        $this->assertEquals(1, $message->attempts);
        $this->assertNotNull($message->sent_at);
    }
}
