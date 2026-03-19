@extends('layouts.admin')

@section('header', $site->name)

@section('main')
<div class="mb-4">
    <a href="{{ route('admin.sites.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; All Sites</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg border border-gray-200 p-5 space-y-3 text-sm">
        <h2 class="font-semibold text-gray-700 mb-3">Site Details</h2>
        <div><span class="text-gray-500 w-36 inline-block">Domain</span>{{ $site->domain }}</div>
        <div><span class="text-gray-500 w-36 inline-block">Notification Email</span>{{ $site->notification_email ?? '—' }}</div>
        <div><span class="text-gray-500 w-36 inline-block">Auth Mode</span>{{ $site->auth_mode?->value ?? '—' }}</div>
        <div><span class="text-gray-500 w-36 inline-block">Captcha</span>{{ $site->captcha_provider?->value ?? '—' }}</div>
        <div><span class="text-gray-500 w-36 inline-block">Tenant</span>{{ $site->tenant?->email ?? '—' }}</div>
        <div><span class="text-gray-500 w-36 inline-block">Status</span>
            @if ($site->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
            @endif
        </div>
        <div><span class="text-gray-500 w-36 inline-block">Created</span>{{ $site->created_at->format('Y-m-d H:i') }}</div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Credentials ({{ $site->credentials->count() }})</h2>
        </div>
        @forelse ($site->credentials as $cred)
            <div class="px-5 py-3 border-b border-gray-50 text-sm text-gray-700">
                {{ $cred->name }} &middot; <span class="text-gray-400">{{ $cred->credential_type?->value }}</span>
            </div>
        @empty
            <p class="px-5 py-4 text-sm text-gray-400">No credentials.</p>
        @endforelse
    </div>
</div>

<div class="mt-6 bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-700">Recent Messages</h2>
    </div>
    @forelse ($site->mailMessages as $msg)
        <div class="px-5 py-3 border-b border-gray-50 text-sm flex items-center justify-between">
            <span class="text-gray-700">{{ $msg->from_email }} &rarr; {{ $msg->subject }}</span>
            <span class="text-gray-400 text-xs">{{ $msg->created_at->diffForHumans() }}</span>
        </div>
    @empty
        <p class="px-5 py-4 text-sm text-gray-400">No messages.</p>
    @endforelse
</div>
@endsection
