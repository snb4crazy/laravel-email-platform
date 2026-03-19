@extends('layouts.admin')

@section('header', 'All Messages')

@section('main')
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if ($messages->isEmpty())
        <p class="px-5 py-8 text-sm text-gray-400 text-center">No messages yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3">From</th>
                        <th class="px-5 py-3">To</th>
                        <th class="px-5 py-3">Subject</th>
                        <th class="px-5 py-3">Site</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Source</th>
                        <th class="px-5 py-3">Received</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($messages as $msg)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-gray-700">{{ $msg->from_email }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $msg->to_email }}</td>
                            <td class="px-5 py-3 text-gray-700 max-w-xs truncate">{{ $msg->subject }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $msg->site?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $msg->status === 'sent' ? 'bg-green-100 text-green-700' : ($msg->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $msg->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-400">{{ $msg->source }}</td>
                            <td class="px-5 py-3 text-gray-400">{{ $msg->created_at->diffForHumans() }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.messages.show', $msg) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection
