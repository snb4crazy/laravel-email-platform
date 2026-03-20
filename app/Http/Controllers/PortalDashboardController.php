<?php

namespace App\Http\Controllers;

use App\Models\MailMessage;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalDashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $sitesCount = Site::query()->where('tenant_id', $user->id)->count();
        $messagesCount = MailMessage::query()->where('tenant_id', $user->id)->count();
        $recentMessages = MailMessage::query()
            ->where('tenant_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
        $recentSites = Site::query()
            ->where('tenant_id', $user->id)
            ->withCount('credentials')
            ->latest()
            ->limit(5)
            ->get();

        return view('portal.dashboard', compact(
            'sitesCount',
            'messagesCount',
            'recentMessages',
            'recentSites',
        ));
    }
}
