<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me'])->middleware('auth:sanctum');
});

// Topics routes
Route::apiResource('topics', App\Http\Controllers\Api\TopicController::class)->except(['store', 'update', 'destroy']);
Route::post('topics', [App\Http\Controllers\Api\TopicController::class, 'store'])->middleware('auth:sanctum');
Route::put('topics/{topic}', [App\Http\Controllers\Api\TopicController::class, 'update'])->middleware('auth:sanctum');
Route::patch('topics/{topic}', [App\Http\Controllers\Api\TopicController::class, 'update'])->middleware('auth:sanctum');
Route::delete('topics/{topic}', [App\Http\Controllers\Api\TopicController::class, 'destroy'])->middleware('auth:sanctum');

// Topic best reply
Route::post('topics/{topic}/replies/{reply}/best', [App\Http\Controllers\Api\TopicController::class, 'setBestReply'])->middleware('auth:sanctum');

// Topic admin actions
Route::post('topics/{topic}/mark-ad', [App\Http\Controllers\Api\TopicController::class, 'markAsAd'])->middleware('auth:sanctum');
Route::post('topics/{topic}/mark-quarrel', [App\Http\Controllers\Api\TopicController::class, 'markAsQuarrel'])->middleware('auth:sanctum');

// Topic charity
Route::post('topics/{topic}/join-charity', [App\Http\Controllers\Api\TopicController::class, 'joinCharity'])->middleware('auth:sanctum');

// Topic replies routes
Route::get('topics/{topic}/replies', [App\Http\Controllers\Api\ReplyController::class, 'index']);
Route::post('topics/{topic}/replies', [App\Http\Controllers\Api\ReplyController::class, 'store'])->middleware('auth:sanctum');

// Replies routes (for update/delete)
Route::apiResource('replies', App\Http\Controllers\Api\ReplyController::class)->except(['index', 'store']);

// Points routes
Route::prefix('points')->middleware('auth:sanctum')->group(function () {
    Route::get('/logs', [App\Http\Controllers\Api\PointController::class, 'logs']);
});

// Badges routes
Route::get('/badges', [App\Http\Controllers\Api\BadgeController::class, 'index']);
Route::get('/badges/me', [App\Http\Controllers\Api\BadgeController::class, 'userBadges'])->middleware('auth:sanctum');

// （原 Admin 后台 API 路由已移除）
