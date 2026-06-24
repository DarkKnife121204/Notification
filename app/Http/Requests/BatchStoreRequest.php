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
            'channel' => ['required', Rule::enum(DeliveryChannel::class)],
            'priority' => ['required', Rule::enum(Priority::class)],
            'idempotency_key' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'recipient_ids' => ['required', 'array'],
            'recipient_ids.*' => ['required', 'integer', 'distinct', 'exists:recipients,external_id'],
        ];
    }
}
