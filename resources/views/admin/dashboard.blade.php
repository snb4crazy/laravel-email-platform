@extends('layouts.admin')

@section('header', 'Dashboard')

@section('main')
{{-- Stats --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Users</p>
        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $usersCount }}</p>
        <a href="{{ route('admin.users.index') }}" class="mt-2 inline-block text-xs text-indigo-600 hover:underline">View all →</a>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Sites</p>
        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $sitesCount }}</p>
        <a href="{{ route('admin.sites.index') }}" class="mt-2 inline-block text-xs text-indigo-600 hover:underline">View all →</a>
    </div>
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Messages</p>
        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $messagesCount }}</p>
        <a href="{{ route('admin.messages.index') }}" class="mt-2 inline-block text-xs text-indigo-600 hover:underline">View all →</a>
    </div>
</div>

{{-- Recent messages --}}
<div class="bg-white rounded-lg border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-200">
        <h2 class="text-sm font-semibold text-gray-700">Recent Messages</h2>
    </div>
    @if ($recentMessages->isEmpty())
        <p class="px-5 py-8 text-sm text-gray-400 text-center">No messages yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3">From</th>
                        <th class="px-5 py-3">Subject</th>
                        <th class="px-5 py-3">Site</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Received</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($recentMessages as $msg)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-gray-700">{{ $msg->from_email }}</td>
                            <td class="px-5 py-3 text-gray-700 max-w-xs truncate">{{ $msg->subject }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $msg->site?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $msg->status === 'sent' ? 'bg-green-100 text-green-700' : ($msg->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $msg->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-400">{{ $msg->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

