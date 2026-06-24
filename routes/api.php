<?php

use App\Http\Controllers\Api\BatchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/batch', [BatchController::class, 'store']);
