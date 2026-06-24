<?php

use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\RecipientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/batch', [BatchController::class, 'store']);

Route::get('/recipients/{recipient:external_id}/messages', [RecipientController::class, 'index']);
