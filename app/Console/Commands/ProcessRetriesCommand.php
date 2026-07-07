<?php

namespace App\Console\Commands;

use App\Services\RetryService;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;

class ProcessRetriesCommand extends Command
{
    protected $signature = 'retry:process';

    public function handle(RetryService $retryService): int
    {
        while (true) {
            foreach ($retryService->due() as $payload) {
                $data = json_decode($payload, true);

                $this->info('Retrying message: ' . $data['message_id']);

                Kafka::publish()
                    ->onTopic($data['topic'])
                    ->withBody([
                        'message_id' => $data['message_id'],
                    ])
                    ->send();

                $retryService->remove($payload);
            }

            sleep(1);
        }

        return self::SUCCESS;
    }
}
