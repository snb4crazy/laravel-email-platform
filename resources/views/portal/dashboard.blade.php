@extends('layouts.portal')

@section('header', 'Dashboard')

@section('main')
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 mb-8">
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">My Sites</p>
        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $sitesCount }}</p>
        <a href="{{ route('sites.index') }}" class="mt-2 inline-block text-xs text-indigo-600 hover:underline">Manage sites &rarr;</a>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Messages</p>
        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $messagesCount }}</p>
        <a href="{{ route('messages.index') }}" class="mt-2 inline-block text-xs text-indigo-600 hover:underline">View messages &rarr;</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Recent Sites</h2>
            <a href="{{ route('sites.create') }}" class="text-xs text-indigo-600 hover:underline">+ New</a>
        </div>
        @forelse ($recentSites as $site)
            <div class="px-5 py-3 border-b border-gray-50 text-sm flex items-center justify-between gap-4">
                <div>
                    <a href="{{ route('sites.show', $site) }}" class="font-medium text-gray-900 hover:underline">{{ $site->name }}</a>
                    <span class="text-gray-400 text-xs ml-2">{{ $site->domain }}</span>
                    <div class="mt-1">
                        <a href="{{ route('sites.credentials.index', $site) }}" class="text-xs text-indigo-600 hover:underline">
                            Manage credentials ({{ $site->credentials_count }})
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if ($site->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                    @endif
                    <a href="{{ route('sites.credentials.create', $site) }}" class="text-xs text-indigo-600 hover:underline">+ Credential</a>
                </div>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-gray-400 text-center">
                No sites yet. <a href="{{ route('sites.create') }}" class="text-indigo-600 hover:underline">Create one</a>.
            </p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Recent Messages</h2>
        </div>
        @forelse ($recentMessages as $msg)
            <div class="px-5 py-3 border-b border-gray-50 text-sm flex items-center justify-between">
                <div>
                    <span class="font-medium text-gray-800">{{ $msg->from_email }}</span>
                    <span class="text-gray-400 text-xs ml-2">{{ $msg->subject }}</span>
                </div>
                <span class="text-gray-400 text-xs">{{ $msg->created_at->diffForHumans() }}</span>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-gray-400 text-center">No messages yet.</p>
        @endforelse
    </div>
</div>
@endsection
