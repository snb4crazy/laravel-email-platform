@extends('layouts.portal')

@section('header', 'New Credential: ' . $site->name)

@section('main')
<div class="max-w-md">
    <a href="{{ route('sites.credentials.index', $site) }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Credentials</a>

    @if ($errors->any())
        <div class="mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sites.credentials.store', $site) }}"
          class="mt-6 bg-white border border-gray-200 rounded-lg p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="name">Label *</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                   placeholder="e.g. Production API Key"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="credential_type">Type *</label>
            <select id="credential_type" name="credential_type" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @foreach ($credentialTypes as $type)
                    <option value="{{ $type->value }}" @selected(old('credential_type') === $type->value)>
                        {{ ucfirst(str_replace('_', ' ', $type->value)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="expires_at">Expires At</label>
            <input id="expires_at" name="expires_at" type="date" value="{{ old('expires_at') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-gray-400">Leave blank for no expiry.</p>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" checked
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <label for="is_active" class="text-sm text-gray-700">Active immediately</label>
        </div>

        <div class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-md text-amber-700 text-xs">
            The secret will be shown <strong>once</strong> after creation. Store it safely.
        </div>

        <button type="submit"
                class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
            Generate Credential
        </button>
    </form>
</div>
@endsection
