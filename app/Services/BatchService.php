<?php

namespace App\Services;

use App\Enums\Priority;
use App\Models\Batch;
use App\Repositories\BatchRepository;
use Illuminate\Support\Facades\DB;
use Junges\Kafka\Facades\Kafka;

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

    private function topicByPriority(string $priority): string
    {
        return match ($priority) {
            Priority::TRANSACTIONAL->value => config('kafka.topics.transactional'),
            Priority::MARKETING->value => config('kafka.topics.marketing'),
        };
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
                    abort(response()->json([
                        'message' => 'Конфликт повтора'
                    ], 409));
                }

                return $existingBatch->loadCount('messages');
            }

            $batch = $this->batchRepository->createBatch($data, $requestHash);

            $messages = $this->batchRepository->createMessages($batch, $data);

            $topic = $this->topicByPriority($data['priority']);

            DB::afterCommit(function () use ($messages, $topic) {
                foreach ($messages as $message) {
                    Kafka::publish()
                        ->onTopic($topic)
                        ->withBody([
                            'message_id' => $message->id,
                        ])
                        ->send();
                }
            });

            return $batch->loadCount('messages');
        });
    }
}
