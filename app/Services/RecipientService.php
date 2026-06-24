<?php

namespace App\Services;

use App\Models\Recipient;
use App\Repositories\RecipientRepository;
use Illuminate\Database\Eloquent\Collection;

class RecipientService
{
    public function __construct(private readonly RecipientRepository $recipientRepository,) {}

    public function getMessages(Recipient $recipient): Collection
    {
        return $this->recipientRepository->getMessagesWithLogs($recipient);
    }
}
