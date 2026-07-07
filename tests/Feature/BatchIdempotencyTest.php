<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Recipient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Junges\Kafka\Facades\Kafka;
use Tests\TestCase;

class BatchIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_request_does_not_create_second_batch(): void
    {
        Kafka::fake();

        $recipient = Recipient::factory()->create();

        $data = [
            'channel' => 'email',
            'priority' => 'marketing',
            'idempotency_key' => 'test-key',
            'message' => 'Test message',
            'recipient_ids' => [
                $recipient->external_id,
            ],
        ];

        $this->postJson('/api/batch', $data)->assertSuccessful();

        $this->postJson('/api/batch', $data)->assertSuccessful();

        $this->assertDatabaseCount('batches', 1);
        $this->assertDatabaseCount('messages', 1);

        $this->assertEquals(1, Batch::count());
    }
}
