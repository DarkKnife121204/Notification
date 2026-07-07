<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class RetryService
{
    private const KEY = 'messages:retry';

    private const BACKOFF = [
        1 => 10,
        2 => 30,
    ];

    public function schedule(int $messageId, string $topic, int $attempts): void
    {
        $delay = self::BACKOFF[$attempts] ?? 60;

        $payload = json_encode([
            'message_id' => $messageId,
            'topic' => $topic,
        ]);

        Redis::zadd(
            self::KEY,
            now()->addSeconds($delay)->timestamp,
            $payload
        );
    }

    public function due(): array
    {
        return Redis::zrangebyscore(
            self::KEY,
            '-inf',
            now()->timestamp
        );
    }

    public function remove(string $payload): void
    {
        Redis::zrem(self::KEY, $payload);
    }
}
