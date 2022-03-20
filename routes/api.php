<?php

use App\Http\Controllers\Api\ItemController;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// membuat route untuk itemnya
Route::apiResource('/items', ItemController::class);