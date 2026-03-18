<?php

use App\Http\Controllers\ContactSubmissionController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

// Test routes without middleware
Route::get('/health', function (): JsonResponse {
    return response()->json([
        'status' => 'ok',
    ]);
});

Route::get('/version', function (): JsonResponse {
    return response()->json([
        'app' => config('app.name'),
        'laravel' => app()->version(),
    ]);
});

Route::post('/contact', [ContactSubmissionController::class, 'store'])
    ->middleware(['draft.contact.auth', 'throttle:30,1']); // 30 submissions / minute per IP

Route::prefix('webhook')->group(function (): void {
    Route::post('/contact-form', [ContactSubmissionController::class, 'webhook'])
        ->middleware('draft.webhook.signature');
});
