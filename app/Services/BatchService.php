<?php

namespace App\Services;

use App\Jobs\SendMessageJob;
use App\Models\Batch;
use App\Repositories\BatchRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class BatchService
{
    public function __construct(
        private readonly BatchRepository $batchRepository,
    ) {}

    private function makeRequestHash(array $data): string
    {
        $recipientIds = $data['recipient_ids'];

        sort($recipientIds);

        return hash('sha256', json_encode([
            'channel' => $data['channel'],
            'priority' => $data['priority'],
            'message' => $data['message'],
            'recipient_ids' => $recipientIds,
        ]));
    }

    public function create(array $data): Batch
    {
        return DB::transaction(function () use ($data) {
            $requestHash = $this->makeRequestHash($data);

            $existingBatch = $this->batchRepository->findByIdempotencyKey(
                $data['idempotency_key']
            );

            if ($existingBatch) {
                if ($existingBatch->request_hash !== $requestHash) {
                    abort(409, 'Конфликт повтора');
                }

                return $existingBatch->loadCount('messages');
            }

            $batch = $this->batchRepository->createBatch($data, $requestHash);

            $messages = $this->batchRepository->createMessages($batch, $data);

            foreach ($messages as $message) {
                SendMessageJob::dispatch($message->id)->afterCommit();
            }

            return $batch->loadCount('messages');
        });
    }
}
