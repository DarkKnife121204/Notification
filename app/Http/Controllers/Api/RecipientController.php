<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RecipientResource;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipientController
{
    public function index(Recipient $recipient): AnonymousResourceCollection
    {
        $messages = $recipient->messages()->with('logs')->latest()->get();

        return RecipientResource::collection($messages);
    }
}
