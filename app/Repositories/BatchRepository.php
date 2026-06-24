<?php

namespace App\Repositories;

use App\Enums\Status;
use App\Models\Batch;
use App\Models\Recipient;

class BatchRepository
{
    public function findByIdempotencyKey(string $idempotencyKey): ?Batch
    {
        return Batch::where('idempotency_key', $idempotencyKey)->first();
    }

    public function createBatch(array $data, string $requestHash): Batch
    {
        return Batch::create([
            'channel' => $data['channel'],
            'priority' => $data['priority'],
            'message' => $data['message'],
            'idempotency_key' => $data['idempotency_key'],
            'request_hash' => $requestHash,
        ]);
    }

    public function createMessages(Batch $batch, array $data): void
    {
        $recipients = Recipient::whereIn('external_id', $data['recipient_ids'])->get();

        foreach ($recipients as $recipient) {
            $batch->messages()->create([
                'recipient_id' => $recipient->id,
                'channel' => $data['channel'],
                'priority' => $data['priority'],
                'status' => Status::default(),
                'message' => $data['message'],
            ]);
        }
    }
}
