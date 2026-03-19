@extends('layouts.portal')

@section('header', 'Credentials: ' . $site->name)

@section('main')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('sites.show', $site) }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to Site</a>
    <a href="{{ route('sites.credentials.create', $site) }}"
       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
        + Add Credential
    </a>
</div>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if ($credentials->isEmpty())
        <p class="px-5 py-8 text-sm text-gray-400 text-center">No credentials yet.</p>
    @else
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Key ID</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Expires</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($credentials as $cred)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $cred->name }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $cred->credential_type?->value }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $cred->key_id }}</td>
                        <td class="px-5 py-3">
                            @if ($cred->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-400">{{ $cred->expires_at?->format('Y-m-d') ?? 'Never' }}</td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('sites.credentials.destroy', [$site, $cred]) }}"
                                  onsubmit="return confirm('Revoke this credential?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Revoke</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
