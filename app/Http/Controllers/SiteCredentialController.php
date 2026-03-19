<?php

namespace App\Http\Controllers;

use App\Enums\CredentialType;
use App\Models\Site;
use App\Models\SiteCredential;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SiteCredentialController extends Controller
{
    public function index(Site $site): View
    {
        $this->authorize('view', $site);

        $credentials = $site->credentials()->latest()->get();

        return view('portal.sites.credentials.index', compact('site', 'credentials'));
    }

    public function create(Site $site): View
    {
        $this->authorize('update', $site);

        return view('portal.sites.credentials.create', [
            'site' => $site,
            'credentialTypes' => CredentialType::cases(),
        ]);
    }

    public function store(Request $request, Site $site): RedirectResponse
    {
        $this->authorize('update', $site);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'credential_type' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        // Generate key_id and a random secret (shown once)
        $keyId = Str::upper(Str::random(12));
        $secret = Str::random(40);

        $site->credentials()->create([
            'name' => $validated['name'],
            'credential_type' => $validated['credential_type'],
            'key_id' => $keyId,
            'secret_hash' => bcrypt($secret),
            'is_active' => ! empty($validated['is_active']),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()
            ->route('sites.credentials.index', $site)
            ->with('status', 'Credential created. Key ID: '.$keyId.' — Secret (shown once): '.$secret);
    }

    public function destroy(Site $site, SiteCredential $credential): RedirectResponse
    {
        $this->authorize('update', $site);

        $credential->delete();

        return redirect()
            ->route('sites.credentials.index', $site)
            ->with('status', 'Credential deleted.');
    }
}
