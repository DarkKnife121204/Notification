<?php

namespace App\Models;

use App\Enums\DeliveryChannel;
use App\Enums\Priority;
use App\Enums\Status;
use App\Models\Traits\LogsStatusChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use LogsStatusChanges;
    protected $fillable = [
        'batch_id',
        'recipient_id',
        'channel',
        'priority',
        'status',
        'message',
        'attempts',
        'sent_at',
        'delivered_at',
        'dropped_at',
        'error_message',
    ];

    protected $casts = [
        'channel' => DeliveryChannel::class,
        'priority' => Priority::class,
        'status' => Status::class,
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'dropped_at' => 'datetime',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }
}
