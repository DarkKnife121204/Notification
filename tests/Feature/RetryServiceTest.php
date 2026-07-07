<?php

namespace Tests\Feature;

use App\Services\RetryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RetryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_retry_is_scheduled_in_redis(): void
    {
        Redis::del('messages:retry');

        $retryService = app(RetryService::class);

        $retryService->schedule(
            messageId: 1,
            topic: 'notifications.marketing',
            attempts: 1,
        );

        $items = Redis::zrange(
            'messages:retry',
            0,
            -1
        );

        $this->assertCount(1, $items);

        $payload = json_decode($items[0], true);

        $this->assertEquals(1, $payload['message_id']);
        $this->assertEquals(
            'notifications.marketing',
            $payload['topic']
        );
    }

    public function test_retry_has_backoff_delay(): void
    {
        Redis::del('messages:retry');

        $retryService = app(RetryService::class);

        $before = now()->timestamp;

        $retryService->schedule(
            messageId: 1,
            topic: 'notifications.marketing',
            attempts: 1,
        );

        $items = Redis::zrange(
            'messages:retry',
            0,
            -1,
            ['WITHSCORES' => true]
        );

        $score = (int) array_values($items)[0];

        $this->assertGreaterThanOrEqual(
            $before + 10,
            $score
        );
    }
}
