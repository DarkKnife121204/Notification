<?php

namespace App\Repositories;

use App\Models\Recipient;
use Illuminate\Database\Eloquent\Collection;

class RecipientRepository
{
    public function getMessagesWithLogs(Recipient $recipient): Collection
    {
        return $recipient->messages()->with('logs')->latest()->get();
    }
}
