<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'channel' => $this->channel,
            'priority' => $this->priority,
            'message' => $this->message,
            'idempotency_key' => $this->idempotency_key,
            'messages_count' => $this->messages_count ?? $this->messages()->count(),
            'created_at' => $this->created_at,
        ];
    }
}
