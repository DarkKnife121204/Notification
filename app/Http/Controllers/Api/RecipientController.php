<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RecipientResource;
use App\Models\Recipient;
use App\Services\RecipientService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipientController
{
    /**
     * Получить историю уведомлений получателя
     *
     * Возвращает текущие статусы уведомлений
     * и историю изменения статусов.
     */

    public function index(Recipient $recipient, RecipientService $service): AnonymousResourceCollection
    {
        $messages = $service->getMessages($recipient);

        return RecipientResource::collection($messages);
    }
}
