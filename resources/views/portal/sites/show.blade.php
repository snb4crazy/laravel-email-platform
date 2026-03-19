@extends('layouts.portal')

@section('header', $site->name)

@section('main')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('sites.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; My Sites</a>
    <div class="flex gap-3">
        <a href="{{ route('sites.edit', $site) }}"
           class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-sm rounded-md hover:bg-gray-50">
            Edit
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg border border-gray-200 p-5 space-y-3 text-sm">
        <h2 class="font-semibold text-gray-700 mb-3">Site Details</h2>
        <div><span class="text-gray-500 w-36 inline-block">Domain</span>{{ $site->domain }}</div>
        <div><span class="text-gray-500 w-36 inline-block">Notification Email</span>{{ $site->notification_email ?? '—' }}</div>
        <div><span class="text-gray-500 w-36 inline-block">Auth Mode</span>{{ $site->auth_mode?->value ?? '—' }}</div>
        <div><span class="text-gray-500 w-36 inline-block">Captcha</span>{{ $site->captcha_provider?->value ?? '—' }}</div>
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
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">API Credentials ({{ $site->credentials->count() }})</h2>
            <a href="{{ route('sites.credentials.create', $site) }}" class="text-xs text-indigo-600 hover:underline">+ Add</a>
        </div>
        @forelse ($site->credentials as $cred)
            <div class="px-5 py-3 border-b border-gray-50 text-sm flex items-center justify-between">
                <div>
                    <span class="font-medium text-gray-800">{{ $cred->name }}</span>
                    <span class="text-gray-400 text-xs ml-2">{{ $cred->credential_type?->value }}</span>
                    <span class="text-gray-400 text-xs ml-2">ID: {{ $cred->key_id }}</span>
                </div>
                @if ($cred->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                @endif
            </div>
        @empty
            <p class="px-5 py-4 text-sm text-gray-400">
                No credentials.
                <a href="{{ route('sites.credentials.create', $site) }}" class="text-indigo-600 hover:underline">Create one</a>.
            </p>
        @endforelse
    </div>
</div>

<div class="mt-6 bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">Recent Messages</h2>
        <a href="{{ route('messages.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
    </div>
    @forelse ($site->mailMessages as $msg)
        <div class="px-5 py-3 border-b border-gray-50 text-sm flex items-center justify-between">
            <span class="text-gray-700">{{ $msg->from_email }} &rarr; {{ $msg->subject }}</span>
            <span class="text-gray-400 text-xs">{{ $msg->created_at->diffForHumans() }}</span>
        </div>
    @empty
        <p class="px-5 py-4 text-sm text-gray-400">No messages for this site.</p>
    @endforelse
</div>
@endsection
