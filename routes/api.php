<?php

use App\Http\Controllers\ContactSubmissionController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

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

Route::post('/contact', [ContactSubmissionController::class, 'store']);

Route::prefix('webhook')->group(function (): void {
    Route::post('/contact-form', [ContactSubmissionController::class, 'webhook']);
});

