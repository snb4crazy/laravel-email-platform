<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailMessage;
use App\Models\Site;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'usersCount' => User::query()->count(),
            'sitesCount' => Site::query()->count(),
            'messagesCount' => MailMessage::query()->count(),
            'recentMessages' => MailMessage::query()
                ->with(['tenant', 'site'])
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
