<?php

namespace App\Http\Requests;

use App\Enums\DeliveryChannel;
use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatchStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            /** Канал доставки уведомления: email или sms */
            'channel' => ['required', Rule::enum(DeliveryChannel::class)],
            /** Приоритет сообщения: transactional или marketing */
            'priority' => ['required', Rule::enum(Priority::class)],
            /** Уникальный ключ идемпотентности запроса */
            'idempotency_key' => ['required', 'string', 'max:255'],
            /** Текст уведомления. */
            'message' => ['required', 'string'],
            /** Массив внешних идентификаторов получателей */
            'recipient_ids' => ['required', 'array'],
            /** Внешний идентификатор получателя */
            'recipient_ids.*' => ['required', 'integer', 'distinct', 'exists:recipients,external_id'],
        ];
    }
}
