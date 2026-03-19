<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\View\View;

class AdminSiteController extends Controller
{
    public function index(): View
    {
        $sites = Site::query()
            ->with('tenant')
            ->latest()
            ->paginate(20);

        return view('admin.sites.index', compact('sites'));
    }

    public function show(Site $site): View
    {
        $site->load(['tenant', 'credentials', 'mailMessages' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.sites.show', compact('site'));
    }
}
