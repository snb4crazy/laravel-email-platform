@extends('layouts.portal')

@section('header', 'Mail Templates')

@section('main')
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $templates->total() }} template(s)</p>
    <a href="{{ route('templates.create') }}"
       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
        + New Template
    </a>
</div>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    @if ($templates->isEmpty())
        <div class="px-5 py-12 text-center">
            <p class="text-sm text-gray-500 mb-3">No templates yet. The system uses a default built-in template.</p>
            <a href="{{ route('templates.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                Create Custom Template
            </a>
        </div>
    @else
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Event Type</th>
                    <th class="px-5 py-3">Subject</th>
                    <th class="px-5 py-3">Default</th>
                    <th class="px-5 py-3">Active</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($templates as $tpl)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $tpl->name }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $tpl->event_type }}</td>
                        <td class="px-5 py-3 text-gray-500 truncate max-w-xs">{{ $tpl->subject_template }}</td>
                        <td class="px-5 py-3">
                            @if ($tpl->is_default)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Default</span>
                            @else
                                &mdash;
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if ($tpl->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 flex items-center gap-3">
                            <a href="{{ route('templates.edit', $tpl) }}" class="text-indigo-600 hover:underline text-xs">Edit</a>
                            <form method="POST" action="{{ route('templates.destroy', $tpl) }}"
                                  onsubmit="return confirm('Delete this template?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $templates->links() }}
        </div>
    @endif
</div>
@endsection
