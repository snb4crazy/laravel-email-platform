@extends('mail.layout')

@section('content')
    <p>Hello, <strong>{{ $senderName }}</strong>!</p>
    <p>We have received your message and will get back to you shortly.</p>

    @if ($subject)
        <p><strong>Subject:</strong> {{ $subject }}</p>
    @endif

    <p><strong>Your message:</strong></p>
    <p>{{ $body }}</p>

    @if ($fileUrl)
        <p><strong>Attached file:</strong> <a href="{{ $fileUrl }}">View file</a></p>
    @endif

    <div class="meta">
        <p><strong>Source:</strong> {{ ucfirst($source) }}</p>
        <p><strong>Received:</strong> {{ $receivedAt }}</p>
    </div>
@endsection

