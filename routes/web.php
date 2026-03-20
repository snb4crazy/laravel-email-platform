<?php

use App\Http\Controllers\Admin\AdminMessageController;
use App\Http\Controllers\Admin\AdminSiteController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MailTemplateController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PortalDashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SiteCredentialController;
use App\Http\Controllers\SiteIntegrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Role-based redirect after login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── User portal ────────────────────────────────────────────────────────
    Route::get('/portal', [PortalDashboardController::class, 'index'])->name('portal.dashboard');

    Route::resource('sites', SiteController::class);

    Route::prefix('sites/{site}')->name('sites.')->group(function (): void {
        Route::get('/integration', [SiteIntegrationController::class, 'show'])->name('integration');
        Route::get('/credentials', [SiteCredentialController::class, 'index'])->name('credentials.index');
        Route::get('/credentials/create', [SiteCredentialController::class, 'create'])->name('credentials.create');
        Route::post('/credentials', [SiteCredentialController::class, 'store'])->name('credentials.store');
        Route::delete('/credentials/{credential}', [SiteCredentialController::class, 'destroy'])->name('credentials.destroy');
    });

    Route::resource('messages', MessageController::class)->only(['index', 'show']);
    Route::resource('templates', MailTemplateController::class)->except(['show']);
});

// ── Admin ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/sites', [AdminSiteController::class, 'index'])->name('sites.index');
    Route::get('/sites/{site}', [AdminSiteController::class, 'show'])->name('sites.show');

    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{mailMessage}', [AdminMessageController::class, 'show'])->name('messages.show');
});

Route::any('/register', function () {
    return response()->json([
        'message' => 'Public signup is disabled. Ask an administrator to provision your account.',
    ], 403);
});
