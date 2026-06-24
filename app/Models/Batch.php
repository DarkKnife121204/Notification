<?php

namespace App\Models;

use App\Enums\DeliveryChannel;
use App\Enums\Priority;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'channel',
        'priority',
        'message',
        'idempotency_key',
        'request_hash',
    ];

    protected $casts = [
        'channel' => DeliveryChannel::class,
        'priority' => Priority::class,
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
