<?php

namespace Tests\Feature;

use App\Models\Recipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Junges\Kafka\Facades\Kafka;
use Tests\TestCase;

class BatchPriorityTest extends TestCase
{
    use RefreshDatabase;

    public function test_messages_are_published_to_topics_by_priority(): void
    {
        Kafka::fake();

        $recipient = Recipient::factory()->create();

        $this->postJson('/api/batch', [
            'channel' => 'email',
            'priority' => 'transactional',
            'idempotency_key' => 'transactional-key',
            'message' => 'Transactional message',
            'recipient_ids' => [$recipient->external_id],
        ])->assertSuccessful();

        $this->postJson('/api/batch', [
            'channel' => 'email',
            'priority' => 'marketing',
            'idempotency_key' => 'marketing-key',
            'message' => 'Marketing message',
            'recipient_ids' => [$recipient->external_id],
        ])->assertSuccessful();

        Kafka::assertPublishedOn(
            'notifications.transactional'
        );

        Kafka::assertPublishedOn(
            'notifications.marketing'
        );
    }
}
