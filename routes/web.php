<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome']);
});

Route::any('/register', function () {
    return response()->json([
        'message' => 'Public signup is disabled. Ask an administrator to provision your account.',
    ], 403);
});

