<?php

namespace Tests\Feature;

use App\Enums\DeliveryChannel;
use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Batch;
use App\Models\Message;
use App\Models\Recipient;
use App\Services\Providers\EmailProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\ConsumedMessage;
use Mockery;
use Tests\TestCase;

class KafkaConsumerTest extends TestCase
{
    use RefreshDatabase;

    public function test_consumer_delivers_email_message(): void
    {
        Kafka::fake();

        config([
            'kafka.consumer_topic' => 'notifications.marketing',
        ]);

        $recipient = Recipient::factory()->create();

        $batch = Batch::create([
            'channel' => DeliveryChannel::EMAIL,
            'priority' => Priority::MARKETING,
            'message' => 'Test message',
            'idempotency_key' => 'consumer-test-key',
            'request_hash' => 'consumer-test-hash',
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

        Kafka::shouldReceiveMessages([
            new ConsumedMessage(
                topicName: 'notifications.marketing',
                partition: 0,
                headers: [],
                body: [
                    'message_id' => $message->id,
                ],
                key: null,
                offset: 0,
                timestamp: 0,
            ),
        ]);

        $this->artisan('kafka:consume-messages')
            ->assertSuccessful();

        $message->refresh();

        $this->assertEquals(Status::DELIVERED, $message->status);
        $this->assertEquals(1, $message->attempts);
        $this->assertNotNull($message->sent_at);
        $this->assertNotNull($message->delivered_at);
    }
}
