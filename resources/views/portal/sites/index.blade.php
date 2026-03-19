@extends('layouts.portal')

@section('header', 'My Sites')

@section('main')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $sites->total() }} site(s)</p>
    <a href="{{ route('sites.create') }}"
       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
        + Add Site
    </a>
</div>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if ($sites->isEmpty())
        <div class="px-5 py-12 text-center">
            <p class="text-sm text-gray-500 mb-3">You haven't added any sites yet.</p>
            <a href="{{ route('sites.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                Add Your First Site
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Domain</th>
                        <th class="px-5 py-3">Auth</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Created</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($sites as $site)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">
                                <a href="{{ route('sites.show', $site) }}" class="hover:underline text-indigo-700">{{ $site->name }}</a>
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $site->domain }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $site->auth_mode?->value ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if ($site->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-400">{{ $site->created_at->format('Y-m-d') }}</td>
                            <td class="px-5 py-3 flex items-center gap-3">
                                <a href="{{ route('sites.edit', $site) }}" class="text-indigo-600 hover:underline text-xs">Edit</a>
                                <form method="POST" action="{{ route('sites.destroy', $site) }}"
                                      onsubmit="return confirm('Delete this site?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $sites->links() }}
        </div>
    @endif
</div>
@endsection
