<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $fillable = [
        'message_id',
        'from_status',
        'to_status',
    ];

    protected $casts = [
        'from_status' => Status::class,
        'to_status' => Status::class,
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
