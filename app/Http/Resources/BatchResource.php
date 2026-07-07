<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /** Идентификатор рассылки */
            'id' => $this->id,
            /** Канал доставки уведомлений */
            'channel' => $this->channel,
            /** Приоритет рассылки */
            'priority' => $this->priority,
            /** Текст уведомления */
            'message' => $this->message,
            /** Ключ идемпотентности запроса. */
            'idempotency_key' => $this->idempotency_key,
            /** Количество созданных сообщений */
            'messages_count' => $this->messages_count
                ?? $this->messages()->count(),
            /** Дата и время создания рассылки */
            'created_at' => $this->created_at,
        ];
    }
}
