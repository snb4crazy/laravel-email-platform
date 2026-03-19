@extends('layouts.portal')

@section('header', 'Message #' . $mailMessage->id)

@section('main')
<div class="mb-4">
    <a href="{{ route('messages.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; All Messages</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg border border-gray-200 p-5 space-y-3 text-sm">
        <h2 class="font-semibold text-gray-700 mb-3">Message Details</h2>
        <div><span class="text-gray-500 w-32 inline-block">From</span>{{ $mailMessage->from_name }} &lt;{{ $mailMessage->from_email }}&gt;</div>
        <div><span class="text-gray-500 w-32 inline-block">To</span>{{ $mailMessage->to_email }}</div>
        <div><span class="text-gray-500 w-32 inline-block">Subject</span>{{ $mailMessage->subject }}</div>
        <div><span class="text-gray-500 w-32 inline-block">Status</span>{{ $mailMessage->status }}</div>
        <div><span class="text-gray-500 w-32 inline-block">Source</span>{{ $mailMessage->source }}</div>
        <div><span class="text-gray-500 w-32 inline-block">Site</span>{{ $mailMessage->site?->name ?? '—' }}</div>
        <div><span class="text-gray-500 w-32 inline-block">Received</span>{{ $mailMessage->created_at->format('Y-m-d H:i:s') }}</div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Events</h2>
        </div>
        @forelse ($mailMessage->events as $event)
            <div class="px-5 py-3 border-b border-gray-50 text-sm text-gray-700">
                <span class="font-medium">{{ $event->type }}</span>
                <span class="text-gray-400 ml-2 text-xs">{{ $event->created_at->format('H:i:s') }}</span>
            </div>
        @empty
            <p class="px-5 py-4 text-sm text-gray-400">No events recorded.</p>
        @endforelse
    </div>
</div>

@if ($mailMessage->body_text || $mailMessage->body_html)
    <div class="mt-6 bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Body</h2>
        <pre class="text-sm text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded-md border border-gray-100">{{ $mailMessage->body_text ?? strip_tags($mailMessage->body_html) }}</pre>
    </div>
@endif
@endsection
