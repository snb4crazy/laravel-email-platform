@csrf

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="name">Site Name *</label>
    <input id="name" name="name" type="text" value="{{ old('name', $site->name ?? '') }}" required
           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="domain">Domain *</label>
    <input id="domain" name="domain" type="text" value="{{ old('domain', $site->domain ?? '') }}"
           placeholder="example.com" required
           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="notification_email">Notification Email</label>
    <input id="notification_email" name="notification_email" type="email"
           value="{{ old('notification_email', $site->notification_email ?? '') }}"
           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <p class="mt-1 text-xs text-gray-400">Where to send form submission notifications.</p>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="auth_mode">Auth Mode *</label>
    <select id="auth_mode" name="auth_mode" required
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @foreach ($authModes as $mode)
            <option value="{{ $mode->value }}" @selected(old('auth_mode', $site->auth_mode?->value ?? '') === $mode->value)>
                {{ ucfirst(str_replace('_', ' ', $mode->value)) }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="captcha_provider">Captcha Provider</label>
    <select id="captcha_provider" name="captcha_provider"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @foreach ($captchaProviders as $provider)
            <option value="{{ $provider->value }}" @selected(old('captcha_provider', $site->captcha_provider?->value ?? 'none') === $provider->value)>
                {{ ucfirst(str_replace('_', ' ', $provider->value)) }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="captcha_site_key">Captcha Site Key</label>
    <input id="captcha_site_key" name="captcha_site_key" type="text"
           value="{{ old('captcha_site_key', $site->captcha_site_key ?? '') }}"
           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1" for="captcha_secret">Captcha Secret</label>
    <input id="captcha_secret" name="captcha_secret" type="password"
           value="{{ old('captcha_secret', $site->captcha_secret ?? '') }}"
           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>

<div class="flex items-center gap-2">
    <input type="checkbox" id="is_active" name="is_active" value="1"
           @checked(old('is_active', $site->is_active ?? true))
           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
    <label for="is_active" class="text-sm text-gray-700">Site is active</label>
</div>
