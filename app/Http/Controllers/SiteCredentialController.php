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
            'secret' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $credentialType = CredentialType::from($validated['credential_type']);

        // Generate key_id and secret (shown once). If user provides a custom
        // secret, we store it encrypted so captcha/HMAC flows can read it later.
        $keyId = Str::upper(Str::random(12));
        $secret = $validated['secret'] ?? Str::random(40);

        $secretHash = null;
        if ($credentialType === CredentialType::API_KEY) {
            $secretHash = bcrypt($secret);
        }

        $site->credentials()->create([
            'name' => $validated['name'],
            'credential_type' => $credentialType,
            'key_id' => $keyId,
            'secret_hash' => $secretHash,
            'secret_encrypted' => $secret,
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
