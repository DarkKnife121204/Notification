<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BatchStoreRequest;
use App\Http\Resources\BatchResource;
use App\Services\BatchService;
use Illuminate\Http\Request;

class BatchController
{
    /**
     * Создать массовую рассылку
     *
     * Создаёт batch уведомлений и отправляет сообщения получателям
     * через Kafka с учётом приоритета.
     */

    public function store(BatchStoreRequest $request, BatchService $service): BatchResource
    {
        $batch = $service->create($request->validated());

        return new BatchResource($batch);
    }
}
