<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_id' => $this->batch_id,
            'channel' => $this->channel,
            'priority' => $this->priority,
            'status' => $this->status,
            'message' => $this->message,
            'attempts' => $this->attempts,
            'sent_at' => $this->sent_at,
            'delivered_at' => $this->delivered_at,
            'dropped_at' => $this->dropped_at,
            'error_message' => $this->error_message,

            'logs' => $this->logs->map(fn ($log) => [
                'from_status' => $log->from_status,
                'to_status' => $log->to_status,
                'created_at' => $log->created_at,
            ]),
        ];
    }
}
