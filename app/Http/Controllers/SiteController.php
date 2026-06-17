<?php

namespace App\Http\Controllers;

use App\Enums\CaptchaProvider;
use App\Enums\SiteAuthMode;
use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function index(): View
    {
        $sites = Site::query()
            ->where('tenant_id', Auth::id())
            ->withCount('credentials')
            ->latest()
            ->paginate(15);

        return view('portal.sites.index', compact('sites'));
    }

    public function create(): View
    {
        return view('portal.sites.create', [
            'authModes' => SiteAuthMode::cases(),
            'captchaProviders' => CaptchaProvider::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255'],
            'notification_email' => ['nullable', 'email', 'max:255'],
            'auth_mode' => ['required', 'string'],
            'captcha_provider' => ['nullable', 'string'],
            'captcha_site_key' => ['nullable', 'string', 'max:255'],
            'captcha_secret' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['tenant_id'] = Auth::id();
        $validated['is_active'] = ! empty($validated['is_active']);
        $validated['public_key'] = Str::upper(Str::random(32));

        Site::query()->create($validated);

        return redirect()->route('sites.index')->with('status', 'Site created successfully.');
    }

    public function show(Site $site): View
    {
        $this->authorize('view', $site);

        $site->load(['credentials', 'mailMessages' => fn ($q) => $q->latest()->limit(10)]);

        return view('portal.sites.show', compact('site'));
    }

    public function edit(Site $site): View
    {
        $this->authorize('update', $site);

        return view('portal.sites.edit', [
            'site' => $site,
            'authModes' => SiteAuthMode::cases(),
            'captchaProviders' => CaptchaProvider::cases(),
        ]);
    }

    public function update(Request $request, Site $site): RedirectResponse
    {
        $this->authorize('update', $site);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255'],
            'notification_email' => ['nullable', 'email', 'max:255'],
            'auth_mode' => ['required', 'string'],
            'captcha_provider' => ['nullable', 'string'],
            'captcha_site_key' => ['nullable', 'string', 'max:255'],
            'captcha_secret' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = ! empty($validated['is_active']);

        $site->update($validated);

        return redirect()->route('sites.show', $site)->with('status', 'Site updated successfully.');
    }

    public function destroy(Site $site): RedirectResponse
    {
        $this->authorize('delete', $site);

        $site->delete();

        return redirect()->route('sites.index')->with('status', 'Site deleted.');
    }
}
