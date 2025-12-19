<?php

use App\Http\Controllers\Api\PostApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Webservice)
|--------------------------------------------------------------------------
|
| Basic read-only endpoints to power PWA/offline features and external clients.
| Auth can be added later (Sanctum) if you want private endpoints.
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/posts', [PostApiController::class, 'index']);
    Route::get('/posts/{post:slug}', [PostApiController::class, 'show']);
    Route::get('/search', [PostApiController::class, 'search']);
});
