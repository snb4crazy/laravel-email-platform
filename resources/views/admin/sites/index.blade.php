@extends('layouts.admin')

@section('header', 'All Sites')

@section('main')
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if ($sites->isEmpty())
        <p class="px-5 py-8 text-sm text-gray-400 text-center">No sites registered yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Domain</th>
                        <th class="px-5 py-3">Tenant</th>
                        <th class="px-5 py-3">Auth Mode</th>
                        <th class="px-5 py-3">Active</th>
                        <th class="px-5 py-3">Created</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($sites as $site)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $site->name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $site->domain }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $site->tenant?->email ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $site->auth_mode?->value ?? '—' }}</td>
                            <td class="px-5 py-3">
                                @if ($site->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-400">{{ $site->created_at->format('Y-m-d') }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.sites.show', $site) }}" class="text-indigo-600 hover:underline text-xs">View</a>
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
