<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            /** Идентификатор сообщения */
            'id' => $this->id,
            /** Идентификатор массовой рассылки */
            'batch_id' => $this->batch_id,
            /** Канал доставки уведомления */
            'channel' => $this->channel,
            /** Приоритет уведомления */
            'priority' => $this->priority,
            /** Текущий статус доставки */
            'status' => $this->status,
            /** Текст уведомления */
            'message' => $this->message,
            /** Количество попыток отправки */
            'attempts' => $this->attempts,
            /** Дата и время передачи сообщения провайдеру */
            'sent_at' => $this->sent_at,
            /** Дата и время подтверждения доставки */
            'delivered_at' => $this->delivered_at,
            /** Дата и время окончательного прекращения попыток доставки */
            'dropped_at' => $this->dropped_at,
            /** Последняя ошибка доставки */
            'error_message' => $this->error_message,
            /** История изменения статусов сообщения */
            'logs' => $this->logs->map(fn ($log) => [
                /** Предыдущий статус */
                'from_status' => $log->from_status,
                /** Новый статус */
                'to_status' => $log->to_status,
                /** Дата и время изменения статуса */
                'created_at' => $log->created_at,
            ]),
        ];
    }
}
